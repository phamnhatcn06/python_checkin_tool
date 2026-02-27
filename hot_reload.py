import sys
import os
import time
import subprocess
import threading

def get_file_mtimes(monitor_extensions):
    mtimes = {}
    for root, dirs, files in os.walk("."):
        if ".git" in dirs:
            dirs.remove(".git")
        if "__pycache__" in dirs:
            dirs.remove("__pycache__")
        
        for file in files:
            if any(file.endswith(ext) for ext in monitor_extensions):
                path = os.path.join(root, file)
                try:
                    mtimes[path] = os.stat(path).st_mtime
                except OSError:
                    pass
    return mtimes

def main():
    target_script = "main.py"
    if len(sys.argv) > 1:
        target_script = sys.argv[1]

    monitor_extensions = [".py", ".qss"] # Monitor python and stylesheet files
    
    print(f"Starting hot reloader for: {target_script}")
    print("Press Ctrl+C to stop.")

    process = None

    def start_process():
        nonlocal process
        if process:
            print("Restarting application...")
            process.terminate()
            try:
                process.wait(timeout=2)
            except subprocess.TimeoutExpired:
                process.kill()
        else:
            print("Starting application...")
        
        # Use the same python interpreter
        process = subprocess.Popen([sys.executable, target_script])

    start_process()
    
    last_mtimes = get_file_mtimes(monitor_extensions)

    try:
        while True:
            time.sleep(1)
            current_mtimes = get_file_mtimes(monitor_extensions)
            
            changed = False
            if set(last_mtimes.keys()) != set(current_mtimes.keys()):
                changed = True # File added/removed
            else:
                for path, mtime in current_mtimes.items():
                    if path not in last_mtimes or last_mtimes[path] != mtime:
                        changed = True
                        break
            
            if changed:
                print("Change detected!")
                start_process()
                last_mtimes = current_mtimes

    except KeyboardInterrupt:
        print("\nStopping hot reloader.")
        if process:
            process.terminate()

if __name__ == "__main__":
    main()
