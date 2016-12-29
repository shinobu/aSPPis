<?php
namespace aSPPis;
require_once('vendor/autoload.php');
use aSPPis\lib\ASPPisParser;
use aSPPis\lib\ASPPisLexer;
use \Exception;
class ASPPisMain
{
    //root gets set in the parser
    public $root = null;
    public $parser = null;

    private function parse($filePointer)
    {
        if (!isset($this->parser)) {
            $this->parser = new ASPPisParser($this);
        }

        $scanner = new ASPPisLexer($filePointer);
        while ($token = $scanner->nextToken()) {
            if ($token->type != -1) {
                print PHP_EOL;
                print $token->value;
                $this->parser->doParse($token->type, $token);
            } else {
                $this->root = null;
                $err = 'Invalid Input in line ' . $token->line . '. Problem with: ' . $token->value . fgets($filePointer, 15);
                throw new Exception($err);
            }
        }
        $this->parser->doParse(0);
        if ($this->root == null) {
            $err = 'Could not finish Parsing';
            throw new Exception($err);
        }
    }

    public function parseString($string)
    {
        $filePointer = fopen("php://memory", 'r+');
        fwrite($filePointer, $string);
        rewind($filePointer);
        return $this->parse($filePointer);
    }

    public function parseFile($file)
    {
        try {
            $filePointer = fopen($file, 'r+');
        } catch (Exception $e) {
            throw new Exception('Unable to open file with path: ' . $file, 0, $e);
        }
        return $this->parse($filePointer);
    }

    public function resetParser()
    {
        //TODO
    }
}
