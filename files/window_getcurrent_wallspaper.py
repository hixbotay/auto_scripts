import os 
from shutil import copyfile
import time



currentFile = "C:/Users/vuonganh.duong/AppData/Roaming/Microsoft/Windows/Themes/TranscodedWallpaper"
newFileName = str(int(time.time()))

copyfile(currentFile, "F:/images/beauty places/"+newFileName+'.jpg')