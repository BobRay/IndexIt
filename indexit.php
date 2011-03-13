<?php
/**
 Creates a book index from a text file in the form:
 *
 * topic1|subtopic, 12, 24, 17
 * topic2|subtopic, 11,13,56
 */

/* ToDo: AddSubHead
    array_merge pages
    have inArray return key
    sorting
 */

class AllHeads {
    public $mainHeads; /* array of mainHead objects */
   
    
    public function __construct()
    {
        $this->mainHeads = array();
    }

    public function display() {
        foreach ($this->mainHeads as $mainHead) {
            echo "\n" . $mainHead->heading;
            if (!empty($mainHead->pages )) {
                $p = implode(', ' , $mainHead->getPages());
                echo ', ' . $p;
            }
            if (!empty ($mainHead->subheads)) {
                foreach($mainHead->subheads as $subHead) {

                    $p = implode(', ' , $subHead->getPages());
                    echo "\n    " . $subHead->heading . ', ' . $p;

                }
            }
        }
    }
    public function addLine($fileLine) {
        $line = new Line($fileLine);

        $key = $this->inArray($line->mainHead);
        if ($key !== null) {
            /* already have that main head */
            if ($line->subHead) {
                $this->addSubHead($line, $key);
            } else {
                /* No subhead, add pages */
                $this->mainHeads[$key]->setPages($line->getPages());

            }

        }  else {
            $this->addMainHead($line);
        }


    }
    public function inArray($s) {
        if (empty($this->mainHeads)) {
            return null;
        }
        $s = trim($s);
        foreach($this->mainHeads as $key=>$head) {
            if (strcasecmp($s, $head->heading)==0) {
                return $key;
            }
        }
        return null;
    }
    public function AddMainHead($line) {
        $head = new MainHead($line);
        $this->mainHeads[] = $head;


    }
    public function addSubHead($line, $key) {
        $this->mainHeads[$key]->addSubHead($line);
    }
}


class Line {
    public $mainHead;
    public $subHead;
    public $pages;
    public function __construct($line ) {
        // echo '<br />Line: ' . $line;
        $this->pages = array();
        $parts = explode('|', $line);
        if (count($parts) == 1) { /* no sub head */
            $splits = explode(',', $line);
            $this->mainHead = trim($splits[0]);
            $this->subHead = null;
            foreach($splits as $split) {
                $split = trim($split);
                if (is_numeric($split)) {
                    $this->pages[] = $split;
                }
            }
        } else {
            $this->mainHead = trim($parts[0]);

            $splits = explode(',', $parts[1]);
            $this->subHead =  trim($splits[0]);
            foreach($splits as $split) {
                $split = trim($split);
                if (is_numeric($split)) {
                    $this->pages[] = trim($split);
                }
            }

        }
        // echo '<br />' . implode('x',$this->pages);

    }
    public function getPages() {
        return $this->pages;
    }
    
}
class SubHead {
    public $heading;
    public $pages;
    public function __construct($line )
    {
        $this->heading = $line->subHead;
        $this->pages = $line->getPages();
    }
    public function getPages() {
        return (array) $this->pages;
    }
}

class MainHead
{
    public $heading;
    public $pages;
    public $subheads;

    static $sortKey;

    public function __construct($line)
    {
        $this->pages = array();
        $this->heading = $line->mainHead;
        if ($line->subHead) {
            $this->subheads[] = new SubHead($line);
        } else {
            $this->pages = $line->getPages();
        }
        
    }
    public function getPages() {
        return (array) $this->pages;
    }
    public function setPages($pages) {
        $this->pages = array_merge($this->pages, $pages);
    }
    public function addSubHead($line) {
        foreach ($this->subheads as $key=>$sub) {
            if (strcasecmp($line->subHead,$sub->heading) ==0) {
                $sub->pages = array_merge($sub->pages,$line->pages);
                return;
            }
        }
        $sub = new SubHead($line);
        $this->subheads[] = $sub;
    }

    public static function sorter( $a, $b )
    {
        return strcasecmp( $a->{self::$sortKey}, $b->{self::$sortKey} );
    }

    public static function sortByProp( &$collection, $prop )
    {
        self::$sortKey = $prop;
        usort( $collection, array( __CLASS__, 'sorter' ) );
    }

}



$fp = fopen('index.txt','r');

if ($fp == false){
    return "File Not Found";
}
$allHeads = new AllHeads;
while (($buffer = fgets($fp, 4096)) !== false) {
       $buffer = trim($buffer);
       if (!empty($buffer)) {
       $allHeads->addLine($buffer);
       }
}
fclose($fp);

$allHeads->display();

