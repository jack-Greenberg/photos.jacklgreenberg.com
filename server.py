from PIL import Image, ImageFile
from PIL.ExifTags import TAGS as EXIF_TAGS
from os.path import join, basename, dirname, realpath
import os
import sys
import time
from watchdog.observers import Observer
from watchdog.events import FileSystemEventHandler

ImageFile.LOAD_TRUNCATED_IMAGES = True

def process_image(img, metadata):
    # Define orientations for rotating the image
    ORIENTATIONS = [None, None, None, -180, None, None, -90, None, -270]

    degree_to_rotate = ORIENTATIONS[metadata.get('Orientation', 0)]
    if degree_to_rotate is not None:
        img = img.rotate(degree_to_rotate, expand = True)
    width, height = img.size
    larger_dimension = width if width > height else height
    scales = [ x / larger_dimension for x in [ 320.0, 640.0, 960.0, 1280.0 ] ]
    new_sizes = [ (int(round(width * s)), int(round(height * s))) for s in scales ]
    return [ img.resize(size, Image.LANCZOS) for size in new_sizes ]

def get_img_data(img):
    try:
        img_exif = img._getexif()
        return {
            EXIF_TAGS[k]: v for k, v in img_exif.items() if k in EXIF_TAGS
        }
    except AttributeError:
        return {}

class MyHandler(FileSystemEventHandler):
    def on_created(self, event):
        if (event.is_directory):
            return
        img = Image.open(event.src_path)
        img_name = event.src_path.split('/')[1].split('.')[0]
        os.mkdir('images/%s' % img_name)
        processed = process_image(img, get_img_data(img))
        widths = [ r.size[0] for r in processed ]

        new_files = [ join('images/%s/' % (img_name), '%s-%d.jpg' % (img_name, w)) for w in widths ]
        for r, f in zip(processed, new_files):
            r.save(f, optimize = True, progressive = True)
            r.close()
        os.remove('tmp/%s' % event.src_path.split('/')[1])

if __name__ == "__main__":
    path = sys.argv[1] if len(sys.argv) > 1 else '.'
    event_handler = MyHandler()
    observer = Observer()
    observer.schedule(event_handler, path, recursive=False)
    observer.start()
    try:
        while True:
            time.sleep(1)
    except KeyboardInterrupt:
        observer.stop()
    observer.join()
