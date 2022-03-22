import os 
dir_path = os.path.dirname(os.path.realpath(__file__))
files = os.listdir(dir_path)
for file in files:
    os.rename(file, file+'.jpg')