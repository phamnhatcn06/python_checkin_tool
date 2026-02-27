from PySide6.QtCore import QObject, Signal, Slot, QThread
from app.api_client import ApiClient

class ApiWorker(QThread):
    result_ready = Signal(dict)
    error_occurred = Signal(str)

    def __init__(self, api_client, qr_code, parent=None):
        super().__init__(parent)
        self.api_client = api_client
        self.qr_code = qr_code

    def run(self):
        attendee = self.api_client.get_attendee(self.qr_code)
        
        if attendee:
            print(f"Found attendee: {attendee['name']}")
            # Record check-in
            checkin_success = self.api_client.check_in(self.qr_code)
            if checkin_success:
                print(f"Check-in recorded for: {self.qr_code}")
            else:
                print(f"Warning: Failed to record check-in for {self.qr_code}")
                
            self.result_ready.emit(attendee)
        else:
            print("Attendee not found")
            self.error_occurred.emit("Mã không hợp lệ hoặc không tìm thấy.")

class Controller(QObject):
    # Signals to update UI
    attendee_scanned = Signal(dict) # Emitted when a valid attendee is found
    scan_error = Signal(str)        # Emitted when scan fails or invalid
    clear_welcome = Signal()        # Optional: auto-clear welcome screen after N seconds

    def __init__(self, api_client: ApiClient = None):
        super().__init__()
        self.api_client = api_client or ApiClient()
        self.scan_counter = 0

    @Slot(str)
    def process_scan(self, qr_code: str):
        print(f"Processing scan: {qr_code}")
        qr_code = qr_code.strip()
        if not qr_code:
            return
            
        # Parse URL if the QR code is a full URL link (e.g. ?code=123)
        import urllib.parse as urlparse
        from urllib.parse import parse_qs
        parsed_url = urlparse.urlparse(qr_code)
        query_params = parse_qs(parsed_url.query)
        if 'code' in query_params:
            qr_code = query_params['code'][0]
            print(f"Parsed URL code: {qr_code}")

        self.scan_counter += 1
        current_scan_id = self.scan_counter

        # Create worker. Parent is self, so it lives as long as Controller. 
        # deleteLater will clean it up safely.
        worker = ApiWorker(self.api_client, qr_code, self)
        
        worker.result_ready.connect(lambda data, sid=current_scan_id: self.handle_result(data, sid))
        worker.error_occurred.connect(lambda err, sid=current_scan_id: self.handle_error(err, sid))
        worker.finished.connect(worker.deleteLater)
        
        worker.start()

    def handle_result(self, data, scan_id):
        if scan_id == self.scan_counter:
            self.attendee_scanned.emit(data)

    def handle_error(self, err, scan_id):
        if scan_id == self.scan_counter:
            self.scan_error.emit(err)
