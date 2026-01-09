<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Parser Playground</title>
        <link rel="stylesheet" href="/static/style.css">
    </head>
    <body>
        <?php
            $input = "";
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $input = $_POST["expression"];        
            }
        ?>
        <h2>Entrada</h2>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <div class="editor-container">   
                <div class="line-numbers" id="lineNumbers">1</div>
                <textarea id="editor" name="expression" placeholder="Escriba su código aquí..."><? echo htmlspecialchars($input)?></textarea>
            </div>
            <input type="submit" value="Run">
        </form>

        <h2>Salida:</h2>
        <div class="console">
            <?php
            require_once("bootstrap.php");

            $grammar = new calc();
            $parser = new parse_engine($grammar);

            if (!empty($input)) {
                try {
                    $parser->reset();
                    $lexer = new Lexer($input);
                    while (true) {
                        $token = $lexer->nextToken();
                        $tok = $token[0];
                        $val = $token[1];
                        if ($tok == "EOF") {
                            $ast = $parser->eat_eof();
                            break;
                        }
                        $parser->eat($tok, $val);
                    }
                    $interpreter = new Interpreter();
                    $ast->accept($interpreter);
                    echo $interpreter->output;
                } catch(Exception $e) {
                    echo "Error: " . $e->getMessage();
                }
            } else {
                echo "Por favor ingrese código para parsear.";
            }
            ?>
        </div>
        <script src="/static/script.js"></script>
    </body>
</html>
