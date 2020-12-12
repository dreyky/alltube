<?php

error_reporting(0);

/*

BAB DUDA IS HERE

Simple PHP Script For Xtream Codes + VLC Player Using youtube-dl TOOLS

REQUIRED LINUX OS WITH ROOT ACCESS

--------------------------------------------------------------------------------------------------------

INSTALL PYTHON

apt-get update

sudo apt-get install python-setuptools

--------------------------------------------------------------------------------------------------------

[NOTE] YT-DL WORKING ONLY IN PYTHON 2, I'WE TESTED ON PYTHON 3 BUT NOT WORKING!!!

INSTALL Python 2

sudo apt install python-pip

IF PYTHON 2 Successfully GO TO youtube-dl INSTALLER IGNORE Python 3

--------------------------------------------------------------------------------------------------------

--------------------------------------------------------------------------------------------------------

INSTALL Python 3

sudo apt install python3-pip 

--------------------------------------------------------------------------------------------------------

https://pip.pypa.io/en/stable/installing/

sudo easy_install pip

curl https://bootstrap.pypa.io/get-pip.py -o get-pip.py

python get-pip.py

--------------------------------------------------------------------------------------------------------

--------------------------------------------------------------------------------------------------------

INSTALL youtube-dl

https://ytdl-org.github.io/youtube-dl/download.html

apt-get update

sudo wget https://yt-dl.org/downloads/latest/youtube-dl -O /usr/local/bin/youtube-dl

IF U SEE ERROR WITH CERTIFICATE USE THIS COMMAND

sudo wget https://yt-dl.org/downloads/latest/youtube-dl -O /usr/local/bin/youtube-dl --no-check-certificate

sudo chmod a+rx /usr/local/bin/youtube-dl

sudo pip install --upgrade youtube_dl

FOR UPDATES sudo youtube-dl -U

PUT PHP FILE IN /var/www/html/YOUR FOLDER

GIVE PERMISSIONS TO 775 OR 777 rwxrwxrwx

--------------------------------------------------------------------------------------------------------

--------------------------------------------------------------------------------------------------------

FOR PANEL USE 127.0.0.1

http://127.0.0.1/youtube_dl//panel.php?url=https://youtu.be/aGeoCHm8OzM

http://192.168.1.10/PHP_Streams/YT_DL/panel.php?url=https://youtu.be/aGeoCHm8OzM

FOR VLC OR KODI USE YOUR REAL IP http://[IP OR HOST]/panel.php?url=https://youtu.be/aGeoCHm8OzM

I Love The 90 - Spain Valencia 2018 (Megamix)

http://127.0.0.1/PHP_Streams/YT_DL/panel.php?url=https://youtu.be/ydyFTgfCWRc

Scantraxx Hardstyle Live Stream

http://192.168.1.10/PHP_Streams/YT_DL/panel.php?url=https://youtu.be/aGeoCHm8OzM

TO GET FORMATS USE IN TERMINAL => youtube-dl --list-formats https://youtu.be/aGeoCHm8OzM

EXAMPLE youtube-dl --list-formats https://youtu.be/aGeoCHm8OzM

OR youtube-dl -F 'https://youtu.be/ydyFTgfCWRc' OR WITHOUT ''

youtube-dl -f 22 https://youtu.be/ydyFTgfCWRc

format code  extension  resolution note

91           mp4        256x144    HLS  194k , avc1.4d400c, 30.0fps, mp4a.40.5@ 48k

92           mp4        426x240    HLS  335k , avc1.4d4015, 30.0fps, mp4a.40.5@ 48k

93           mp4        640x360    HLS  808k , avc1.4d401e, 30.0fps, mp4a.40.2@128k

94           mp4        854x480    HLS  994k , avc1.4d401f, 30.0fps, mp4a.40.2@128k

95           mp4        1280x720   HLS 1490k , avc1.4d401f, 30.0fps, mp4a.40.2@256k (best)

FOR SELECTED USE youtube-dl -f [CHANGE THIS => 94] -g

FOR AUTOMATIC LEAVE  youtube-dl -f best -g

I RECOMMEND YOU DO NOT USE COMMAND "bestvideo+bestaudio/best" OR ANY SMILAR COMMANDS IN PHP GET, USE IN TERMINAL ONLY THESE!!!

HELP youtube-dl --help

--------------------------------------------------------------------------------------------------------

*/

$get_url = $_GET["url"];

if (is_null($get_url))

{

echo "Enter Youtube Link";

}

else

{

// youtube-dl -f best -g IS AUTO SELECTED

$youtube_downloader = "youtube-dl -f best -g ".$get_url." 2>&1";

$stream = exec($youtube_downloader);

header("Location: ".$stream);

echo $stream;

// echo readfile($stream);

// echo readgzfile($stream);

}

?>
