<?php
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
$file = UploadedFile::fake()->create("test.webm", 1024);
$v = Validator::make(["videos" => [$file]], ["videos.*" => "nullable|mimes:mp4,mov,avi|max:15360"]);
var_dump($v->errors()->all());
?>
