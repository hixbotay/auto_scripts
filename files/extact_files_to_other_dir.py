#extact file from input files path to other directory with keeping directory tree
#Copy the script to relative directory
#input: input.txt
"""Example: 
app\Http\Enums\ServiceType.php
app\Http\Controllers\Payment\PaymentController.php
app\Modules\Payment\Payment.php
"""
"""Output
output\app\Http\Enums\ServiceType.php
output\app\Http\Controllers\Payment\PaymentController.php
output\app\Modules\Payment\Payment.php
"""
import os 
import shutil

dir_path = os.path.dirname(os.path.realpath(__file__))
files = os.listdir(dir_path)

def readFileToArray(fileName):
    s = open(fileName, 'r').read()
    r = s.split("\n")
    return r

if not os.path.isdir(dir_path+'/output'):
    os.makedirs(dir_path+'/output')
    
targetFiles = readFileToArray('input.txt')
for file in targetFiles:
    src_path = os.path.join(dir_path,file)
    dst_path = os.path.join(dir_path,"output",file)
    dst_dir = os.path.dirname(dst_path)
    src_dir = os.path.dirname(src_path)
    if not os.path.isdir(dst_dir):
        os.makedirs(dst_dir)

    shutil.copy(src_path, dst_path)