<?php 

class EAN13 {
    private $code = null;
    private $prefix = false;
    
    public function __construct ($prefix = false) {
        $this->prefix = $prefix === false ? false : (string) $prefix;
    }
    
    public static function create($prefix = false) {
        $barcode = new static($prefix);
        
        return $barcode->generate();
    }

    public function generate()
    {
        // Generate random
        $this->code = (string) mt_rand(100000000000, 999999999999); // 12 chars long
        
        if ($this->prefix) {
            $this->code = $this->prefix . $this->code;
            $this->code = substr($this->code, 0, -strlen($this->prefix));
        }

        // Get latest digit
        $this->code .= $this->getCheckDigit();

        return $this->code;
    }

    private function getCheckDigit()
    {
        $codePartials = str_split($this->code);
        $checkdigit = null;
        $evenNumbers = 0;
        $oddNumbers = 0;

        foreach ($codePartials as $key => $value) {
            if (($key + 1) % 2 == 0) { // Keys start from 0, We want the start to be 1
                $evenNumbers += $value;
            } else {
                $oddNumbers += $value;
            }
        }

        $evenNumbers = $evenNumbers * 3;
        $total = $evenNumbers + $oddNumbers;

        if ($total % 10 == 0) {
            $checkdigit = 0;
        } else {
            $next_multiple = $total + (10 - $total % 10);

            $checkdigit = $next_multiple - $total;
        }

        return $checkdigit;
    }
}
