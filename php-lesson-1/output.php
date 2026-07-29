<?php 
function echoPairedHTML(string $tag, string $content) {
    echo "
        <$tag>
            $content
        </$tag>
    ";
}
function echoUnpairedHTML(string $tag, string $content) {
    echo "
        <$tag $content>
    ";
}
