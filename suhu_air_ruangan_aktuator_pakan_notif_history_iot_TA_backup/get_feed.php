<?php
if(!file_exists("feed.txt")){
  file_put_contents("feed.txt","0");
}
echo trim(file_get_contents("feed.txt"));
