import os 
from PIL import Image
from sys import argv

def compressFolder(dir_path):
    print("Directory: "+dir_path)
    files = os.listdir(dir_path)
    total = len(files)
    i=1
    for file in files:
        file_path = os.path.join(dir_path,file)
        if os.path.isdir(file_path):
            compressFolder(file_path)
        else:
            f,ext = os.path.splitext(file)
            if(ext == '.jpg' or ext == '.png' or ext == '.jpeg'): 
                
                img = Image.open(file_path)
                width, height = img.size
                if width > 1920:            
                    img = img.resize((1920,int(1920*height/width)))
                
                img.save(file_path,
                         optimize = True)
                print(str(i)+"/"+str(total))
                i+=1

if len(argv) < 2:
    print("Please input directory")
    exit(0)
dir_path = argv[1]

compressFolder(dir_path)