<?php 

namespace App\Enums;


enum LessonType:string{
    case Video='video';
    case Artical='artical';
    case Quiz='quiz';
    case Live='live';
    case Assignment='assignment';
    case File='file';
}