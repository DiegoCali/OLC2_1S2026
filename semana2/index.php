<!DOCTYPE html>
<html>
<title>Calculadora</title>
<body>
<?php
    $input = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $input = $_POST["expression"];        
    }
?>

    <h1>Calculadora Simple</h1>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
        <input type="text" name="expression" placeholder="Ingrese una expresión matemática">
        <input type="submit" value="Calcular">
    </form>
<?php
require_once("../lime/parse_engine.php");
require_once("calc.class");
require_once("Lexer.php");

$parser = new parse_engine(new calc());

echo "<h2>Resultado:</h2>";
if (!empty($input)) {
    try {
        $parser->reset();
        $lexer = new Lexer($input);
        while (true) {
            $token = $lexer->nextToken();
            $tok = $token[0];
            $val = $token[1];

            if ($tok == "EOF") {
                $parser->eat_eof();
                break;
            }
            $parser->eat($tok, $val);
        }
    }catch(Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Por favor ingrese una expresión matemática.";
}
?>
</body>
</html>


