<?php
/**
 Creates a book index from a text file in the form:
 *
 * topic1|subtopic, 12, 24, 17
 * topic2|subtopic, 11,13,56
 */




class AllHeads {
    public $mainHeads; /* array of mainHead objects */

    
    public function __construct()
    {
        $this->mainHeads = array();
    }

    public function inlineCode(&$head, $mainHead, $html)
    {
        /* does inlineCode tokens #string~ */

        /* fix {core_path} sorting */
        $head = str_replace('!{core_path}','{core_path}',$head);

        /* directories */

        if ($mainHead == 'directories') { /* do subheads */
            if ($head == 'core') {
                $head = '#' . $head . '~';
            } elseif ( !strstr($head,'ing') && !strstr($head, 'XAMPP')) {
                $head = preg_replace('/^([a-z\/\_]*)/', '#$1~', $head);

            }

        } else { /* do mainHeads */
            if(! strstr($head,'and') && ! strstr($head, 'same')&& !strstr($head, 'Chmod') && !strstr($head,'source') && ! strstr($head,'target')) {
            $head = preg_replace('/([\-a-z\/\_]*)( directory)/', '#$1~$2', $head);

            }
        }
        /* loops and PHP statements */
        $head = preg_replace('/([a-z]*)(\sloop)/', '#$1~$2', $head);
        $head = preg_replace('/([a-z]*)(\sstatement)/', '#$1~$2', $head);

        /* Files and URLs */

        /* files that don't start with a dot and URLs*/

        $head = preg_replace('/([^\s\|]+\.[^\s,\(0-9]+)/', '#$1~', $head);
        /* files that start with a dot */
        if (substr($head, 0, 1) == '.') {
            $head = preg_replace('/(\.\S*)/', '#$1~', $head);
        }


        /* @ bindings */
        $head = preg_replace('/(@[A-Z]*)/', '#$1~', $head);
        /* variables */
        $head = preg_replace('/(\$.[A-Za-z->_\'\[\]]*[^,\s\)])/', '#$1~', $head);
        /* functions and methods */
        $head = preg_replace('/([\*a-zA-Z_-]*\(\))/', '#$1~', $head);

        /* php operators */
        $head = preg_replace('/(^[\=\&\+]+|\|[\=\&\+]+)/', '#$1~', $head);

        /* System Events */
        $head = preg_replace('/(On[A-Z][a-zA-Z]*)/', '#$1~', $head);

        /* output modifiers */


        if (strstr($mainHead, 'output modifiers')) {
            if (!stristr($head, 'custom') && !stristr($head, 'conditional') && !stristr($head, 'string') && !stristr($head, 'evolution') && !strstr($head,'use with')) {
                $head = '#' . $head . '~';
            }
            ;

        } else {
            $head = preg_replace('/([0-9a-zA-Z_-]*)(\s\(output modifier\))/', '#$1~$2', $head);
        }

        /* permissions in main heads */
        $head = preg_replace('/([a-z_]*)(\spermission$)/', '#$1~$2', $head);
        
        /* settings, constants, permissions, and misc */
        if (!strstr($head, '#')) {  /* don't reprocess methods */
            $head = preg_replace('/([\{\}A-Za-z0-9:]+_[A-Za-z0-9_\{\}]+)/', '#$1~', $head);
        }


        /* extra permissions */
        if ($mainHead == 'permissions') {
            if ($head == 'create' || $head == 'edit' || $head == 'list' or $head == 'load' || $head == 'remove' || $head == 'save' || $head == 'undelete' || $head == 'publish' || $head == 'unpublish' || $head == 'view') {
                $head = '#' . $head . '~';
            }
        }
        /* extra constants ECHO, HTML, FILE */
        $head = preg_replace('/(^[A-Z]*)(\sconstant)/', '#$1~$2', $head);
        if ($mainHead == 'constants') {
            $head = str_replace('ECHO', '#ECHO~', $head);
            $head = str_replace('HTML', '#HTML~', $head);
            $head = str_replace('FILE', '#FILE~', $head);
        }



        /* ToDo: Resource fields */
        /* $mainHead = 'resource fields || 'Create/Edit Resource panel'  do subHeads */
        if ($mainHead == 'resource fields' || $mainHead == 'Create/Edit Resource panel') {
            /* link_attributes (Link Attributes) */
            $head = preg_replace('/^([a-z_]*)(\s\([\sa-zA-Z_]*)(\))/', '#$1~$2$3', $head);

            /* Summary (introtext) */
            $head = preg_replace('/^([a-z_A-Z\s]*\s\()([\sa-z_]*)(\))/', '$1#$2~$3', $head);
        } else {
            /* do mainHeads */
            /* Summary (introtext) resource field */
            $head = preg_replace('/(\()([a-z_]*)(\)\sresource field)/', '$1#$2~$3', $head);

            /* pagetitle (Resource Title) resource field */
            $head = preg_replace('/^([a-z_]*)(\s\([a-z_A-Z_\s]*\)\sresource field)/', '#$1~$2', $head);
        }

        /* anomalies*/
        /* context property (for link tags), scheme property (for link tags) loginResourceId property
        (Login) takeMeBack property (SPForm), validate property (FormIt), startId property (Wayfinder)*/
        if ($mainHead == 'properties') {
            $head = str_replace('context','#context~',$head);
            $head = str_replace('scheme','#scheme~',$head);
            $head = str_replace('loginResourceId','#loginResourceId~',$head);
            $head = str_replace('takeMeBack','#takeMeBack~',$head);
            $head = str_replace('validate','#validate~',$head);
            $head = str_replace('startId','#startId~',$head);
        } else {
            $head = str_replace('context property','#context~ property',$head);
            $head = str_replace('scheme property','#scheme~ property',$head);
            $head = str_replace('loginResourceId property','#loginResourceId~ property',$head);
            $head = str_replace('takeMeBack property','#takeMeBack~ property',$head);
            $head = str_replace('validate property','#validate~ property',$head);
            $head = str_replace('startId property','#startId~ property',$head);
        }

        /* misc. */
        $head = str_replace('lexicon->#load()~','#lexicon->load()~',$head);
        $head = str_replace('--ff-only', '#--ff-only~', $head);
        $head = str_replace('emailsender', '#emailsender~', $head);
        $head = str_replace('emailsubject', '#emailsubject~', $head);
        $head = str_replace('SHA1', '#SHA1~', $head);
        $head = preg_replace('/(\[\[\S*)/', '#$1~', $head);
        $head = str_replace('PBKDF2', '#PBKDF2~', $head);
        if ($head == 'md5') {
            $head = '#md5~';
        }
        if ($head == 'utf-8') {
            $head = '#utf-8~';
        }
        if ($mainHead == 'contexts' && ($head == 'web' || $head == 'mgr' || $head == 'alt')) {
            $head = '#' . $head . '~';
        }
        $head = str_replace('web context', '#web~ context', $head);
        $head = str_replace('mgr context', '#mgr~ context', $head);
        $head = str_replace('alt context', '#alt~ context', $head);

        if ($html) {
            if (! $mainHead) {  /* it's a main head */
                $head = str_replace('#','<span class="indexinlinecodebold">',$head);
            } else { /* it's a subhead */
                $head = str_replace('#','<span class="indexinlinecode">',$head);
            }
            $head = str_replace('~','</span>',$head);

        }
    }


    public function pageSort(&$pages) {
        $introPages=array();
        $mainPages=array();
        $appendixPages=array();

        foreach ($pages as $page){
            $c = substr($page,0,1);
            if ($c == 'A') {
                $appendixPages[] = $page;
            } elseif (preg_match('/[ixvl]/',$c)) {
                $introPages[] = $page;
            } else {
                $mainPages[] = $page;
            }
        }
        if (!empty($introPages)) {
            natsort($introPages);
        }
        if (!empty($mainPages)) {
            natsort($mainPages);
        }
        if (!empty($appendixPages)) {
            natsort($appendixPages);
        }
        $pages = array_merge($introPages, $mainPages, $appendixPages);

        return;
}


    public function display($fp, $html) {
        MainHead::sortByProp($this->mainHeads,'heading');
        $letter = 'A';

        if ($html) {
                $htmlHead = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title></title>
</head>
<body>
<p class="IndexTitle">Index</p>
<hr class="IndexTitleHr" size="2" width="90%" align="left" />
';
                fwrite($fp,$htmlHead);
            }

        foreach ($this->mainHeads as $mainHead) {
            $l = strtoupper(substr($mainHead->heading,0,1));


            if ($l != $letter){
                if (preg_match("/[A-Z\s]/i", $l)) {
                    //echo "\n\n" . $l;
                    if (!$html) {
                        fwrite($fp,"\n\n" . $l);
                    } else {
                        fwrite($fp,'<p class="indexletterheading">' . $l . "</p> \n" .
'<hr class="IndexLetterHeadingHr"  size="2"  width="40%" align="left" />' . "\n");
                    }
                }
                $letter = $l;
            }
            // echo "\n" . $mainHead->heading;
            $this->inlineCode($mainHead->heading,null,$html);
            if (!$html) {
                fwrite($fp,"\n" . $mainHead->heading);
            } else {
                fwrite($fp,"\n" . '<p class="indexmainhead"><span class = "indexmainheadtext">' . $mainHead->heading . '</span>');
            }

            $p = $mainHead->getPages();
            if (!empty($p)) {
                // natsort($p);
                $this->pageSort(&$p);
                $s = implode(', ' , $p);
                // echo ', ' . $s;
                if (!$html) {
                    fwrite($fp, ', ' . $s);
                } else {
                    fwrite($fp,'<span class="indexmainheadnumbers">' . ', ' . $s . '</span></p>');

                }
            }
            if (!empty ($mainHead->subheads)) {
                SubHead::sortByProp($mainHead->subheads,'heading');
                foreach($mainHead->subheads as $subHead) {
                    $p = $subHead->getPages();
                    $this->pageSort(&$p);
                    //natsort($p);
                    $s = implode(', ' , $p);
                    // echo "\n    " . $subHead->heading . ', ' . $s;
                    $d = substr($subHead->heading,0,1) == '('? '': ', ';
                    if (!$html) {
                        $pfx = "\n    ";
                    } else {
                        $pfx = '';
                    }
                    // $pfx = "~";
                    //fwrite($fp,"\n    " . $subHead->heading . ', ' . $s);
                    $this->inlineCode($subHead->heading, $mainHead->heading, $html);
                    if (!$html) {
                        fwrite($fp,$pfx . $subHead->heading . $d . $s);
                    } else {
                        fwrite($fp, "\n" . $pfx .'<p class=indexsubhead><span class="indexsubheadtext">'. $subHead->heading . $d .
                                    '</span><span class="indexsubheadnumbers">' . $s . '</span></p>');
                    }
                }
            }
        }
        if ($html) {
            fwrite($fp,'</body>
</html>');
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
            array_shift($splits); /* remove mainHead from array */
            $this->subHead = null;
            foreach($splits as $split) {
                $split = trim($split);
                $this->pages[] = $split;
            }
        } else {
            $this->mainHead = trim($parts[0]);
            $splits = explode(',', $parts[1]);
            $this->subHead =  trim($splits[0]);
            
            array_shift($splits); /* remove subHead */
            foreach($splits as $split) {
                $split = trim($split);
                $this->pages[] = trim($split);
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
    static $sortKey;

    public function __construct($line )
    {
        $this->heading = $line->subHead;
        $this->pages = $line->getPages();
    }
    public function getPages() {
        return (array) $this->pages;
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

        if (!empty($this->subheads)) {
            foreach ($this->subheads as $key=>$sub) {
                    if (strcasecmp($line->subHead,$sub->heading) ==0) {
                    $sub->pages = array_merge($sub->pages,$line->pages);
                    return;
                }
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

$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;

$html = 1;
$infile = 'bookindex.txt';
if (!$html) {
    $outfile = 'final.txt';
} else {
    $outfile = 'final.html';
}
$fp = fopen($infile,'r');

if ($fp == false){
    return "File Not Found";
}
$allHeads = new AllHeads;
while (($buffer = fgets($fp, 4096)) !== false) {
       $buffer = trim($buffer);
       /* skip comments and empty lines */
       if (empty($buffer) || preg_match('/^#/', $buffer)) {
            continue;
       }
       $allHeads->addLine($buffer);
}
fclose($fp);
$fp = fopen($outfile,'w');

if ($fp == false){
   return "File Not Found";
                             }
$allHeads->display($fp, $html);

fclose($fp);
$mtime= microtime();
$mtime= explode(" ", $mtime);
$mtime= $mtime[1] + $mtime[0];
$tend= $mtime;
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);

echo "\n\nExecution time: {$totalTime}\n";