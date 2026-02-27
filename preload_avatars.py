import os
import hashlib
import requests
import json

def get_db_connection():
    try:
        import mysql.connector
        return mysql.connector.connect(
            host="127.0.0.1",
            user="root",
            password="123456a@",
            database="checkin"
        )
    except ImportError:
        print("mysql-connector-python required. Install with pip install mysql-connector-python")
        return None

def preload_avatars():
    base_dir = os.path.dirname(os.path.abspath(__file__))
    cache_dir = os.path.join(base_dir, "assets", "avatars")
    os.makedirs(cache_dir, exist_ok=True)
    
    conn = get_db_connection()
    if not conn:
        return
        
    cursor = conn.cursor(dictionary=True)
    cursor.execute("SELECT id, name, photo_url FROM attendees WHERE photo_url IS NOT NULL AND photo_url != ''")
    attendees = cursor.fetchall()
    
    print(f"Found {len(attendees)} attendees with photos. Starting preload...")
    
    success = 0
    skipped = 0
    errors = 0
    
    for a in attendees:
        url = a['photo_url']
        name = a['name']
        
        url_hash = hashlib.md5(url.encode('utf-8')).hexdigest()
        cached_path = os.path.join(cache_dir, f"{url_hash}.jpg")
        
        if os.path.exists(cached_path):
            print(f"[{skipped+success+1}/{len(attendees)}] Skipped (already cached): {name}")
            skipped += 1
            continue
            
        try:
            print(f"[{skipped+success+1}/{len(attendees)}] Downloading for: {name} ...", end=" ")
            # Use same proxy bypass as app
            response = requests.get(url, timeout=10, proxies={"http": None, "https": None})
            if response.status_code == 200:
                with open(cached_path, 'wb') as f:
                    f.write(response.content)
                print("OK")
                success += 1
            else:
                print(f"Failed (HTTP {response.status_code})")
                errors += 1
        except Exception as e:
            print(f"Error ({e})")
            errors += 1
            
    print(f"\nPreload Complete! Downloaded: {success}, Skipped: {skipped}, Errors: {errors}")

if __name__ == "__main__":
    preload_avatars()
