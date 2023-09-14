<?php
namespace App\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Image;

class Media
{ 

  /**
   * The upload path
   */
  protected $upload_path;

  /**
   * The storage path
   */
  protected $storage_path;

  /**
   *  Force lowercase for filenames
   */

  protected $force_lowercase = true;

  /**
   *  Image file types
   */

  protected $previewable_image_types = [
    'png', 'jpg', 'jpeg', 'gif'
  ];
  
  /**
   * Constructor
   */
  public function __construct($opts = array())
  {
    $this->storage_path = storage_path('app/public/uploads');
    $this->upload_path = storage_path('app/public/uploads/temp');

    if (isset($opts['force_lowercase']))
    {
      $this->force_lowercase = $opts['force_lowercase'];
    }

    if (!File::isDirectory($this->upload_path))
    {
      File::makeDirectory($this->upload_path, 0775, true, true);
    }

  }

  /**
   * Store a file to the storage
   * 
   * @param  \Illuminate\Http\Request $request
   * @param  String $destinationFolder
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request, $destinationFolder = NULL)
  {
    $file = $request->file('file');
    $file_data = getimagesize($file);
    $name = $this->sanitize(trim($file->getClientOriginalName()), $this->force_lowercase);
    $filename = $this->uniqueFileName($name);
    $file->move($this->upload_path, $filename);
    $filetype = File::extension($this->upload_path . DIRECTORY_SEPARATOR . $filename);
    $filesize = File::size($this->upload_path . DIRECTORY_SEPARATOR . $filename);

    $previewable = $file_data !== FALSE ? $this->previewable($file_data, $filetype) : FALSE;

    return [
      'name' => $filename, 
      'original_name' => $name, 
      'image_orientation' => ($file_data !== FALSE && $file_data[0] > $file_data[1]) ? 'landscape' : 'portrait',
      'extension' => $filetype, 
      'size' => $filesize,
      'preview' => $previewable ? TRUE : FALSE
    ];
  }

  /**
   * Copy a file from temp to storage folder
   * 
   * @param String $filename
   * @param String $folder
   */
  public function copy($filename = NULL, $folder = NULL)
  {
    if ($folder)
    {
      if (!File::isDirectory($this->storage_path . DIRECTORY_SEPARATOR . $folder))
      {
        File::makeDirectory($this->storage_path . DIRECTORY_SEPARATOR . $folder, 0775, true, true);
      }

      $newFilename = $this->uniqueFileName($filename, $folder);

      return 
        Storage::move(
          'public/uploads/temp' . DIRECTORY_SEPARATOR . $filename,
          'public/uploads' . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $newFilename
        );
    }

    return 
      Storage::move(
        'public/uploads/temp' . DIRECTORY_SEPARATOR . $filename,
        'public/uploads' . DIRECTORY_SEPARATOR . $filename
      );
  }

  /**
   * Remove a bunch of files from storage
   * 
   * @param Array $files
   */
  public function removeMany($files = NULL, $temp = FALSE)
  {
    foreach($files as $file)
    {
      if ($temp)
      {
        Storage::delete('public/uploads/temp' . DIRECTORY_SEPARATOR . $file->name);
      }
      else
      {
        if ($file->folder)
        {
          Storage::delete('public/uploads' . DIRECTORY_SEPARATOR . $file->folder . DIRECTORY_SEPARATOR . $file->name);
        }
        else {
          Storage::delete('public/uploads' . DIRECTORY_SEPARATOR . $file->name);
        }
      }
    }
    return true;
  }

  /** 
   * Remove a folder from storage
   * 
   * @param String $folder
   */

  public function removeFolder($folder = NULL)
  {
    return Storage::deleteDirectory('public/uploads' . DIRECTORY_SEPARATOR . $folder);
  }

  /**
   * Removes a file from storage
   * 
   * @param String $filename
   */
  public function remove($filename = NULL, $temp = FALSE)
  {
    if ($temp)
    {
      return Storage::delete('public/uploads/temp' . DIRECTORY_SEPARATOR . $filename);
    }
    return Storage::delete('public/uploads' . DIRECTORY_SEPARATOR . $filename);
  }

  /**
   * Sanitize a filename
   *
   * @param str $filename
   * @param boolean  $force_lowercase - Force the string to lowercase?
   * @param boolean  $anal - If set to *true*, will remove all non-alphanumeric characters.
   */

  protected function sanitize($filename, $force_lowercase = true, $anal = true)
  {
    $filename = $this->remove_accents($filename);
    $strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "=", "+", "[", "{", "]", "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;", "â€”", "â€“", ",", "<", ">", "/", "?");
    $clean = trim(str_replace($strip, "", strip_tags($filename)));
    $clean = preg_replace('/\s+/', "-", $clean);
    $clean = ($anal) ? preg_replace("/[^a-zA-Z0-9._\-]/", "", $clean) : $clean ;
    return ($force_lowercase) ? (function_exists('mb_strtolower')) ? mb_strtolower($clean, 'UTF-8') : strtolower($clean) : $clean;
  }

  /**
   * Remove accents from a string
   * see: https://developer.wordpress.org/reference/functions/remove_accents/
   * @param str $text
   * @param str $locale
   * @return str
   */
  protected function remove_accents($text, $locale = 'de_CH')
  {
    if ( ! preg_match( '/[\x80-\xff]/', $text ) )
    {
      return $text;
    }
    
    /**
      * Unicode sequence normalization from NFD (Normalization Form Decomposed)
      * to NFC (Normalization Form [Pre]Composed), the encoding used in this function.
      */
    if ( function_exists( 'normalizer_is_normalized' ) && function_exists( 'normalizer_normalize' ) )
    {
      if ( ! normalizer_is_normalized( $text ) )
      {
        $text = normalizer_normalize( $text );
      }
    }

    $chars = array(
      // Decompositions for Latin-1 Supplement.
      'ª' => 'a',
      'º' => 'o',
      'À' => 'A',
      'Á' => 'A',
      'Â' => 'A',
      'Ã' => 'A',
      'Ä' => 'A',
      'Å' => 'A',
      'Æ' => 'AE',
      'Ç' => 'C',
      'È' => 'E',
      'É' => 'E',
      'Ê' => 'E',
      'Ë' => 'E',
      'Ì' => 'I',
      'Í' => 'I',
      'Î' => 'I',
      'Ï' => 'I',
      'Ð' => 'D',
      'Ñ' => 'N',
      'Ò' => 'O',
      'Ó' => 'O',
      'Ô' => 'O',
      'Õ' => 'O',
      'Ö' => 'O',
      'Ù' => 'U',
      'Ú' => 'U',
      'Û' => 'U',
      'Ü' => 'U',
      'Ý' => 'Y',
      'Þ' => 'TH',
      'ß' => 's',
      'à' => 'a',
      'á' => 'a',
      'â' => 'a',
      'ã' => 'a',
      'ä' => 'ae',
      'å' => 'a',
      'æ' => 'ae',
      'ç' => 'c',
      'è' => 'e',
      'é' => 'e',
      'ê' => 'e',
      'ë' => 'e',
      'ì' => 'i',
      'í' => 'i',
      'î' => 'i',
      'ï' => 'i',
      'ð' => 'd',
      'ñ' => 'n',
      'ò' => 'o',
      'ó' => 'o',
      'ô' => 'o',
      'õ' => 'o',
      'ö' => 'o',
      'ø' => 'o',
      'ù' => 'u',
      'ú' => 'u',
      'û' => 'u',
      'ü' => 'u',
      'ý' => 'y',
      'þ' => 'th',
      'ÿ' => 'y',
      'Ø' => 'O',
      // Decompositions for Latin Extended-A.
      'Ā' => 'A',
      'ā' => 'a',
      'Ă' => 'A',
      'ă' => 'a',
      'Ą' => 'A',
      'ą' => 'a',
      'Ć' => 'C',
      'ć' => 'c',
      'Ĉ' => 'C',
      'ĉ' => 'c',
      'Ċ' => 'C',
      'ċ' => 'c',
      'Č' => 'C',
      'č' => 'c',
      'Ď' => 'D',
      'ď' => 'd',
      'Đ' => 'D',
      'đ' => 'd',
      'Ē' => 'E',
      'ē' => 'e',
      'Ĕ' => 'E',
      'ĕ' => 'e',
      'Ė' => 'E',
      'ė' => 'e',
      'Ę' => 'E',
      'ę' => 'e',
      'Ě' => 'E',
      'ě' => 'e',
      'Ĝ' => 'G',
      'ĝ' => 'g',
      'Ğ' => 'G',
      'ğ' => 'g',
      'Ġ' => 'G',
      'ġ' => 'g',
      'Ģ' => 'G',
      'ģ' => 'g',
      'Ĥ' => 'H',
      'ĥ' => 'h',
      'Ħ' => 'H',
      'ħ' => 'h',
      'Ĩ' => 'I',
      'ĩ' => 'i',
      'Ī' => 'I',
      'ī' => 'i',
      'Ĭ' => 'I',
      'ĭ' => 'i',
      'Į' => 'I',
      'į' => 'i',
      'İ' => 'I',
      'ı' => 'i',
      'Ĳ' => 'IJ',
      'ĳ' => 'ij',
      'Ĵ' => 'J',
      'ĵ' => 'j',
      'Ķ' => 'K',
      'ķ' => 'k',
      'ĸ' => 'k',
      'Ĺ' => 'L',
      'ĺ' => 'l',
      'Ļ' => 'L',
      'ļ' => 'l',
      'Ľ' => 'L',
      'ľ' => 'l',
      'Ŀ' => 'L',
      'ŀ' => 'l',
      'Ł' => 'L',
      'ł' => 'l',
      'Ń' => 'N',
      'ń' => 'n',
      'Ņ' => 'N',
      'ņ' => 'n',
      'Ň' => 'N',
      'ň' => 'n',
      'ŉ' => 'n',
      'Ŋ' => 'N',
      'ŋ' => 'n',
      'Ō' => 'O',
      'ō' => 'o',
      'Ŏ' => 'O',
      'ŏ' => 'o',
      'Ő' => 'O',
      'ő' => 'o',
      'Œ' => 'OE',
      'œ' => 'oe',
      'Ŕ' => 'R',
      'ŕ' => 'r',
      'Ŗ' => 'R',
      'ŗ' => 'r',
      'Ř' => 'R',
      'ř' => 'r',
      'Ś' => 'S',
      'ś' => 's',
      'Ŝ' => 'S',
      'ŝ' => 's',
      'Ş' => 'S',
      'ş' => 's',
      'Š' => 'S',
      'š' => 's',
      'Ţ' => 'T',
      'ţ' => 't',
      'Ť' => 'T',
      'ť' => 't',
      'Ŧ' => 'T',
      'ŧ' => 't',
      'Ũ' => 'U',
      'ũ' => 'u',
      'Ū' => 'U',
      'ū' => 'u',
      'Ŭ' => 'U',
      'ŭ' => 'u',
      'Ů' => 'U',
      'ů' => 'u',
      'Ű' => 'U',
      'ű' => 'u',
      'Ų' => 'U',
      'ų' => 'u',
      'Ŵ' => 'W',
      'ŵ' => 'w',
      'Ŷ' => 'Y',
      'ŷ' => 'y',
      'Ÿ' => 'Y',
      'Ź' => 'Z',
      'ź' => 'z',
      'Ż' => 'Z',
      'ż' => 'z',
      'Ž' => 'Z',
      'ž' => 'z',
      'ſ' => 's',
      // Decompositions for Latin Extended-B.
      'Ə' => 'E',
      'ǝ' => 'e',
      'Ș' => 'S',
      'ș' => 's',
      'Ț' => 'T',
      'ț' => 't',
      // Euro sign.
      '€' => 'E',
      // GBP (Pound) sign.
      '£' => '',
      // Vowels with diacritic (Vietnamese). Unmarked.
      'Ơ' => 'O',
      'ơ' => 'o',
      'Ư' => 'U',
      'ư' => 'u',
      // Grave accent.
      'Ầ' => 'A',
      'ầ' => 'a',
      'Ằ' => 'A',
      'ằ' => 'a',
      'Ề' => 'E',
      'ề' => 'e',
      'Ồ' => 'O',
      'ồ' => 'o',
      'Ờ' => 'O',
      'ờ' => 'o',
      'Ừ' => 'U',
      'ừ' => 'u',
      'Ỳ' => 'Y',
      'ỳ' => 'y',
      // Hook.
      'Ả' => 'A',
      'ả' => 'a',
      'Ẩ' => 'A',
      'ẩ' => 'a',
      'Ẳ' => 'A',
      'ẳ' => 'a',
      'Ẻ' => 'E',
      'ẻ' => 'e',
      'Ể' => 'E',
      'ể' => 'e',
      'Ỉ' => 'I',
      'ỉ' => 'i',
      'Ỏ' => 'O',
      'ỏ' => 'o',
      'Ổ' => 'O',
      'ổ' => 'o',
      'Ở' => 'O',
      'ở' => 'o',
      'Ủ' => 'U',
      'ủ' => 'u',
      'Ử' => 'U',
      'ử' => 'u',
      'Ỷ' => 'Y',
      'ỷ' => 'y',
      // Tilde.
      'Ẫ' => 'A',
      'ẫ' => 'a',
      'Ẵ' => 'A',
      'ẵ' => 'a',
      'Ẽ' => 'E',
      'ẽ' => 'e',
      'Ễ' => 'E',
      'ễ' => 'e',
      'Ỗ' => 'O',
      'ỗ' => 'o',
      'Ỡ' => 'O',
      'ỡ' => 'o',
      'Ữ' => 'U',
      'ữ' => 'u',
      'Ỹ' => 'Y',
      'ỹ' => 'y',
      // Acute accent.
      'Ấ' => 'A',
      'ấ' => 'a',
      'Ắ' => 'A',
      'ắ' => 'a',
      'Ế' => 'E',
      'ế' => 'e',
      'Ố' => 'O',
      'ố' => 'o',
      'Ớ' => 'O',
      'ớ' => 'o',
      'Ứ' => 'U',
      'ứ' => 'u',
      // Dot below.
      'Ạ' => 'A',
      'ạ' => 'a',
      'Ậ' => 'A',
      'ậ' => 'a',
      'Ặ' => 'A',
      'ặ' => 'a',
      'Ẹ' => 'E',
      'ẹ' => 'e',
      'Ệ' => 'E',
      'ệ' => 'e',
      'Ị' => 'I',
      'ị' => 'i',
      'Ọ' => 'O',
      'ọ' => 'o',
      'Ộ' => 'O',
      'ộ' => 'o',
      'Ợ' => 'O',
      'ợ' => 'o',
      'Ụ' => 'U',
      'ụ' => 'u',
      'Ự' => 'U',
      'ự' => 'u',
      'Ỵ' => 'Y',
      'ỵ' => 'y',
      // Vowels with diacritic (Chinese, Hanyu Pinyin).
      'ɑ' => 'a',
      // Macron.
      'Ǖ' => 'U',
      'ǖ' => 'u',
      // Acute accent.
      'Ǘ' => 'U',
      'ǘ' => 'u',
      // Caron.
      'Ǎ' => 'A',
      'ǎ' => 'a',
      'Ǐ' => 'I',
      'ǐ' => 'i',
      'Ǒ' => 'O',
      'ǒ' => 'o',
      'Ǔ' => 'U',
      'ǔ' => 'u',
      'Ǚ' => 'U',
      'ǚ' => 'u',
      // Grave accent.
      'Ǜ' => 'U',
      'ǜ' => 'u',
    );
    
    /**
      * German has various locales (de_DE, de_CH, de_AT, ...) with formal and informal variants.
      * There is no 3-letter locale like 'def', so checking for 'de' instead of 'de_' is safe,
      * since 'de' itself would be a valid locale too.
      */
    if ( str_starts_with( $locale, 'de' ) ) {
      $chars['Ä'] = 'Ae';
      $chars['ä'] = 'ae';
      $chars['Ö'] = 'Oe';
      $chars['ö'] = 'oe';
      $chars['Ü'] = 'Ue';
      $chars['ü'] = 'ue';
      $chars['ß'] = 'ss';
    } elseif ( 'da_DK' === $locale ) {
      $chars['Æ'] = 'Ae';
      $chars['æ'] = 'ae';
      $chars['Ø'] = 'Oe';
      $chars['ø'] = 'oe';
      $chars['Å'] = 'Aa';
      $chars['å'] = 'aa';
    } elseif ( 'ca' === $locale ) {
      $chars['l·l'] = 'll';
    } elseif ( 'sr_RS' === $locale || 'bs_BA' === $locale ) {
      $chars['Đ'] = 'DJ';
      $chars['đ'] = 'dj';
    }

    $text = strtr( $text, $chars );

    return $text;
  }

  /**
   * Get the formatted size of a file
   *
   * @param $size
   * @return Number $size
   */

  protected function filesize($size)
  {
    $units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $power = $size > 0 ? floor(log($size, 1024)) : 0;
    return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
  }

  /**
   * Check if file is previewable
   *
   * @param Array $file_data
   * @param String $filetype
   * @return Boolean
   */
 
  protected function previewable($file_data = NULL, $filetype = NULL)
  {
    if (in_array(strtolower($filetype), $this->previewable_image_types))
    {
      if ($file_data[0] <= 6000)
      {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Generate a unique filename by adding an incrementable number at the end
   * 
   * Expample: 
   * - file.png => file-1.png
   * - file-1.png => file-2.png
   */
  protected function uniqueFileName($filename, $folder = NULL)
  {
    $current_name = pathinfo($filename, PATHINFO_FILENAME);
    $name = $current_name;
    $extension = pathinfo($filename, PATHINFO_EXTENSION);

    if ($folder)
    {
      // check in destination folder
      while(file_exists($this->storage_path . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $name . '.' . $extension))
      {           
        $name = $name . '-' .  uniqid();
      }
    }
    else 
    {
      // check in temp folder
      $current_name = str_replace($extension, '', $name);
      $name = $current_name;

      while(file_exists($this->upload_path . DIRECTORY_SEPARATOR . $name . '.' . $extension))
      {           
        $name = $name . '-' .  uniqid();
      }
    }

    return $name . '.' . $extension;
  }
  
}
