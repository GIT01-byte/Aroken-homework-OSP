<?php 
function echoPairedHTMLCycle(string $tag, string $content, int $iterations) {
    for ($i = 0; $i < $iterations; $i++) {
        echo "
            <$tag>
                $content
            </$tag>
        ";
    }
}
function echoUnpairedHTMLCycle(string $tag, string $content, int $iterations) {
    for ($i = 0; $i < $iterations; $i++) {
        echo "<$tag $content>";
    }
}
