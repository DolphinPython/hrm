<?php 
require 'simple_html_dom.php';

$html = file_get_html('https://www.google.com/search?lr=&sca_esv=a8e43bc9143f75b8&as_qdr=all&tbm=lcl&q=doctor+in+patna&rflfq=1&num=10&sa=X&ved=2ahUKEwjgwJWJmu2JAxVBUGcHHcUUO1IQjGp6BAgnEAE&biw=1229&bih=643&dpr=1.56#rlfi=hd:;si:16313374135815796689,l,Cg9kb2N0b3IgaW4gcGF0bmFI-8eX3qGtgIAIWhcQABgAGAIiD2RvY3RvciBpbiBwYXRuYZIBBmRvY3RvcqoBRBABKgoiBmRvY3RvcigAMh8QASIb2Uszt-SHYcrD5ttU-bREa4eXqAF-B4rdR7jaMhMQAiIPZG9jdG9yIGluIHBhdG5h;mv:[[25.634518999999997,85.2012599],[25.5795714,85.0319404]];tbs:lrf:!1m4!1u3!2m2!3m1!1e1!1m4!1u2!2m2!2m1!1e1!2m1!1e2!2m1!1e3!3sIAE,lf:1,lf_ui:2');



//$ret = $html->find('.ad_head');

foreach($html->find('h2') as $element)
    echo $element->plaintext . '<br>';

/*foreach($html->find('a') as $element)
    echo $element->href . '<br>';*/















/*require 'simple_html_dom.php';

$html = file_get_html('http://www.google.com/');
$title = $html->find('title', 0);
$image = $html->find('gNO89b', 0);

echo $title->plaintext."<br>\n";
echo $image->src;


$html = file_get_html('https://www.google.com/localservices/prolist?g2lbs=AOHF13mm7Dw5AgT4UF4-HqzSIOz4BWiiGIDrMZ4jL71_FPYI-l684yIHsN17aJjgYuOVZEriCdHx&hl=en-IN&gl=in&ssta=1&q=tutor+in+delhi&oq=tutor+in+delhi&scp=ChVnY2lkOnR1dG9yaW5nX3NlcnZpY2USRxISCS22fjRb_Qw5EUCWOBW3BXIDGhIJkbeSa_BfYzARphNChaFPjNciBURlbGhpKhQNUzXuEBU5r8wtHbVFNxEleE0aLjAAGgV0dXRvciIOdHV0b3IgaW4gZGVsaGkqBVR1dG9y&slp=MgBSAggCYACSAakCCg0vZy8xMXNtbm5uZ2xzCg0vZy8xMXRuNjlqbDU0CgsvZy8xdGR3NmY5ZAoNL2cvMTFneF96aDNxNAoNL2cvMTFoNGs3MTUzagoNL2cvMTFycTB3azUwXwoNL2cvMTF2a3c2cG16aAoNL2cvMTFmam1jMjlsOQoNL2cvMTFidzJoNGpxMAoNL2cvMTFoM2t6X3JoOAoNL2cvMTFuMGtua3ZrNAoNL2cvMTFxbDZqZDU3cwoNL2cvMTFnNndjODkzNwoML2cvMWhjMnc0NF80Cg0vZy8xMWwxZDA5dDFfCg0vZy8xMWZucjliNmc5Cg0vZy8xMXRmXzQ0Z2M2Cg0vZy8xMWgzY2JzdHNwCg0vZy8xMXcyOTBiMV83Cg0vZy8xMWptX2p2YzFkmgEGCgIXGRAA&src=2&serdesk=1&sa=X&ved=2ahUKEwjwn5O-3NuJAxWOSmwGHcORGJwQjGp6BAgnEAE');

/*foreach($html->find('img') as $element)
    echo $element->src . '<br>';

foreach($html->find('a') as $element)
    echo $element->href . '<br>';


//$x= $html->find('<div class="rgnuSb xYjf2e">');
//echo $x->plaintext."<br>\n";

//////////////////////////////

//$ret = $html->find('.I9iumb');

// Find an element by its ID
$element = $html->find('.E94Gcd', 0);

// Do something with the element, like echo its text content
if($element) {
    echo $element->plaintext;
}


//print_r($ret); // <div id="hello">foo</div><div id="world" class="bar">World!</div>




// Create a DOM object from a string of HTML or a URL
$html = file_get_html('https://www.justdial.com/Delhi/Physicians/nct-10892680?trkid=62053320-delhi&term=');

// Find an element by its ID

foreach( $html->find('."jsx-915cb403736563fc resultbox_title_anchor  line_clamp_1"') as $element)
{
    echo $element->plaintext . '<br>';
}  
/*    
$element = $html->find('.ad_head', 0);

// Do something with the element, like echo its text content
if($element) {
    echo $element->plaintext;
}
*/















?>