import os 
from PIL import Image

dir_path = os.path.dirname(os.path.realpath(__file__))

files = os.listdir(dir_path)
total = len(files)
i=1
for file in files:
    f,ext = os.path.splitext(file)
    if(ext == '.jpg' or ext == '.png' or ext == '.jpeg'):    
        img = Image.open(file)
        width, height = img.size
        if width > 1920:            
            img = img.resize((1920,int(1920*height/width)))
        
        img.save(os.path.join(dir_path,"output",file),
                 optimize = True)
        print(str(i)+"/"+str(total))
        i+=1