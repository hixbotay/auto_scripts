import moviepy.editor as mp
clip = mp.VideoFileClip("C:/Users/vuonganh.duong/Videos/Captures/export-job.mp4")
clip_resized = clip.resize(width=1080) # make the height 360px ( According to moviePy documenation The width is then computed so that the width/height ratio is conserved.)
clip_resized.write_videofile("movie_resized.mp4")