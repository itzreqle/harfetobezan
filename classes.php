<?php

class functions
{
    // <Main Functions
    public function FetchArray($query, $con)
    {
        $result = array('Result' => false, 'Response' => 'Database Failed!');
        $sqlArray = $con->query($query) or print(json_encode($result));
        $Arr = array();
        while ($ar = $sqlArray->fetch_assoc())
            $Arr[] = $ar;
        return $Arr;
    }

    public function FetchAssoc($query, $con)
    {
        $result = array('Result' => false, 'Response' => 'Database Failed!');
        $sqlAssoc = $con->query($query) or print(json_encode($result));
        $sqlAssoc = $sqlAssoc->fetch_assoc();

        return $sqlAssoc;
    }

    public function Redirect($path)
    {
        echo "<script>window.location.href = '$path';</script>";
    }

    public function ReIndexArray($arr, $startAt = 0)
    {
        return (0 == $startAt)
            ? array_values($arr)
            : array_combine(range($startAt, count($arr) + ($startAt - 1)), array_values($arr));
    }

    public function ItemRemoveArray($item, $arr)
    {
        $res = array_diff($arr, array($item));
        return $res;
    }

    public function Breadcrumbs($separator = '', $home = 'Home page')
    {
        $path = array_filter(explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
        $base = ($_SERVER['HTTPS'] ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
        $breadcrumbs = array("<a href=\"$base\">$home</a>");
        $last = end(array_keys($path));
        foreach ($path as $x => $crumb) {
            $title = ucwords(str_replace(array('.php', '_'), array('', ' '), $crumb));
            if ($x != $last)
                $breadcrumbs[] = "<a href=\"$base$crumb\">" . urldecode($title) . "</a>";
            else
                $breadcrumbs[] = urldecode($title);
        }
        return implode($separator, $breadcrumbs);
    }

    public function GenerateRandom($length = 8)
    {
        $chars =  'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' .
            '0123456789`-=~!@#$%^&*()_+,./<>?;:[]{}\|';

        $str = '';
        $max = strlen($chars) - 1;

        for ($i = 0; $i < $length; $i++)
            $str .= $chars[random_int(0, $max)];

        return $str;
    }

    public function GenerateRandomLink($length = 8)
    {
        $chars =  'abcdefghijklmnopqrstuvwxyz' .
            '0123456789';

        $str = '';
        $max = strlen($chars) - 1;

        for ($i = 0; $i < $length; $i++)
            $str .= $chars[random_int(0, $max)];

        return $str;
    }

    public function UploadImage($file)
    {
        $target_dir = "/content/uploads/pictures/";
        $file_name = "img_" . rand(1000000, 99999999) . basename($file["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (file_exists($target_file)) {
            die('File exist!');
        }

        if ($file["size"] > 1024 * 100) {
            die('File too large!');
        }

        $allow = ["jpg", "png", "jpeg", "gif", "svg", "webp"];
        // Allow certain file formats
        if (!in_array(strtolower($imageFileType), $allow)) {
            die('Not allowed!');
        }


        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            return $file_name;
        } else {
            die('upload failed!');
        }
    }

    public function UploadFile($file)
    {
        $target_dir = "/content/uploads/files/";
        $file_name = "file_" . rand(1000000, 99999999) . basename($file["name"]);
        $target_file = $target_dir . $file_name;
        $FileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (file_exists($target_file)) {
            die('File exist!');
        }

        if ($file["size"] > 1024 * 1000) {
            die('File too large!');
        }

        $allow = ["docx", "doc", "pdf", "mp3", "mp4", "wav", "avi", "mov", "zip", "xlsx"];
        // Allow certain file formats
        if (!in_array(strtolower($FileType), $allow)) {
            die('Not allowed!');
        }


        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            return $file_name;
        } else {
            die('Upload failed!');
        }
    }

    // />

}
