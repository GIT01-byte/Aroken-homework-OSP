<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP lesson</title>
</head>
<body>
    <div class="div">
        <?php

        // 1 task
        include_once("./output.php");

        echoPairedHTML("span", "123");
        echoUnpairedHTML("br", "");

        // 2 task
        include_once("./output-cycle.php");

        echoPairedHTMLCycle("span", "123", 5);
        echoUnpairedHTMLCycle("br", "", 5);

        // 3 task
        include_once("./counter-even.php");

        $array = [12, 412, 89, 98, 123, 876, true, 987865, 9087654, 261419, "dslafp"];

        echoEvenInArray($array);
        ?>
    </div>
</body>
</html>