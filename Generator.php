<?php

namespace d3vy\Lorum;

class Generator {
    const
        WORD        = 0,
        BASEWORD    = 1,
        WORDTYPE    = 2,
        DEBUG_MODE  = true,
        NORMAL_MODE = false;

    public static function get($debug = self::NORMAL_MODE) {
        $str = '';

        if($debug) {
            self::printStyle();
        }

        if(self::getContents($text, $debug)) {
            self::parse($text, $str, $debug);
            self::fixOpenings($str);
            self::fixExceptions($str);
        }

        return trim($str);
    }

    private static function getContents(&$var, $debug) {
        try {
            $var = json_decode(
                file_get_contents('http://www.lorumipse.hu/generate/')
            );
        } catch(\Exception $ex) {
            if($debug) {
                print_r($ex->getMessage());
            }
            return false;
        }

        return true;
    }

    private static function parse($text, &$str, $debug) {
        foreach($text as $sentence) {
            foreach($sentence as $word) {
                if($debug) {
                    $w = '<span class="lorum-word">' . $word[self::WORD] . '
                            <i>Base: "' . $word[self::BASEWORD] . '"<br>Type: "' . $word[self::WORDTYPE] . '"</i>
                          </span>';
                } else {
                    $w = $word[self::WORD];
                }

                if($word[self::WORDTYPE] == 'PUNCT') {
                    switch($word[self::WORD]) {
                        case '.':
                        case '!':
                        case '?':
                        case ',':
                        case ';':
                        case ':':
                        case '...':
                        case '!!!':
                        case '???':
                        case ')':
                        case ']':
                        case '”':
                            $str .= "{$w} ";
                            break;
                        case '-':
                        case '/':
                            $str .= $w;
                            break;
                        default:
                            $str .= " {$w}";
                    }
                } else {
                    $str .= " {$w}";
                }
            }
        }
    }

    private static function fixOpenings(&$str) {
        $openings = array(
            '(',
            '[',
            '„'
        );
        foreach($openings as $opening) {
            $str = str_replace("{$opening} ", $opening, $str);
        }
    }

    private static function fixExceptions(&$str) {
        $exceptions = array(
            '. )' => '.)',
            '. ]' => '.]',
            ') .' => ').',
            '] .' => '].'
        );
        foreach($exceptions as $from => $to) {
            $str = str_replace($from, $to, $str);
        }
    }

    private static function printStyle() {
        echo '<style>
                span.lorum-word { display: inline-block; }
                span.lorum-word > i { background: white; border: 1px solid black; display: none; font-family: monospace; font-style: normal; position: fixed; bottom: 0; left: 0; text-align: center; width: 100%; z-index: 1; }
                span.lorum-word:hover > i { display: block; }
              </style>';
    }
}