import os 
from PIL import Image

dir_path = os.path.dirname(os.path.realpath(__file__))

files = os.listdir(dir_path)
for file in files:
    f,ext = os.path.splitext(file)
    if(ext == '.jpg' or ext == '.png'):    
        img = Image.open(file)
        width, height = img.size
        cut_width = height*0.1*(width/height)        
        box = (cut_width/2, 0, width - cut_width, height*0.95)
        crop = img.crop(box)
        crop.save(os.path.join(dir_path,"output",file))