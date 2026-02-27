import requests
import json
import os

class ApiClient:
    def __init__(self, config_filename="config.json"):
        # Load the base URL from config file
        self.base_url = "http://localhost/PHP_API/api/index.php" # Fallback
        
        try:
            # Resolve relative to the project root
            base_dir = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
            config_path = os.path.join(base_dir, config_filename)
            
            if os.path.exists(config_path):
                with open(config_path, 'r', encoding='utf-8') as f:
                    config = json.load(f)
                    self.base_url = config.get("api_base_url", self.base_url)
        except Exception as e:
            print(f"Failed to load config file: {e}")

    def get_attendee(self, qr_code: str):
        """
        Calls the PHP API to get attendee info by QR Code.
        Returns a dictionary or None if not found/error.
        """
        if not qr_code:
            return None

        # Strip trailing slashes and index.php for path routing
        base = self.base_url
        if base.endswith('/index.php'):
            base = base[:-10]
        if base.endswith('/'):
            base = base[:-1]
            
        url = f"{base}/api/getAttendee?qr_code={qr_code}"
        try:
            response = requests.get(url, timeout=5, proxies={"http": None, "https": None})
            if response.status_code == 200:
                try:
                    data = response.json()
                    if data.get("success"):
                        return data.get("data")
                except json.JSONDecodeError as e:
                    print(f"API Connection Error (get_attendee): {e}")
                    import sys
                    sys.stdout.buffer.write(f"Raw API Response: {response.text}\n".encode('utf-8'))
                    return None
            else:
                 print(f"API returned status code: {response.status_code}. Response: {response.text}")
            return None
        except requests.exceptions.RequestException as e:
            print(f"API Connection Error (get_attendee): {e}")
            return None

    def check_in(self, qr_code: str):
        """
        Calls the PHP API to mark the attendee as checked in.
        Returns True if successful, False otherwise.
        """
        if not qr_code:
            return False

        base = self.base_url
        if base.endswith('/index.php'):
            base = base[:-10]
        if base.endswith('/'):
            base = base[:-1]
            
        url = f"{base}/api/checkIn"
        payload = {"qr_code": qr_code}
        try:
            # Although the API accepts GET or POST, POST is best practice for state changes
            response = requests.post(url, data=payload, timeout=5, proxies={"http": None, "https": None})
            if response.status_code == 200:
                 data = response.json()
                 return data.get("success", False)
            return False
        except requests.exceptions.RequestException as e:
            print(f"API Connection Error (check_in): {e}")
            return False
