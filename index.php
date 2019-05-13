<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Dinamic Menu Test</title>
	<style>
		.active {
			color: red;
			font-weight: bold;
		}
		ul {
			list-style-position: inside;
			margin: 2px;
			padding-left: 10px;
		}
		body {
			margin: 0;
			padding: 0;
			font: 12px Verdana, Arial, Helvetica, sans-serif;
			color: #000;
		}
		.menu {
			margin: 20px;
		}
		.container {
			border-radius: 10px;

			margin: 20px;
			width: 236px;
			height: auto;
			overflow: hidden;
			background: #deeded;
		}
		.nav {
			font-size: 10px;
			padding: 10px 20px;
		}
		a {
			color: black;
		}
		.container a {
			text-decoration: none;
			color: black;
		}
	</style>
</head>
<body>


<?php 

session_start();

include "array.php";

// set the Id for selected item
if (isset($_GET['id'])) {
    $id = $_GET['id'];
	$_SESSION["id"] = $id;
}
elseif (isset($_SESSION["id"])) 
	$id = $_SESSION["id"];
else
    $id = 0;

// initilization variables
$element = null; // selected item
$parents = [];
$nav_elements = [];

// find selected item/element
foreach ($navigacija as $value) {
    if ($value['id'] == $id) {
        $element = $value;
        break;
    }
}

// create $parents array who has list of all parents id, and set $nav_elements array for nav component
$tmp_parent = $element['parent'];
while ($tmp_parent != 0) {
    foreach ($navigacija as $value) {
        if ($tmp_parent == $value['id']) {
            array_push($parents, $tmp_parent);
            array_push($nav_elements, $value);
            $tmp_parent = $value['parent'];
            break;
        }
    }
}

// set first element for $parents and $nav_elements
array_push($parents, 0);
array_push($nav_elements, ['id'=>'0', 'ime'=>'Pocetna', 'parent'=>'0']);

$menu_elements = []; // array of items for main menu
// filling the array with elements for display
foreach ($navigacija as $value) { 
    if (in_array($value['parent'], $parents) || $element['id'] == $value['parent']) {
        if ($value['parent'] == 0)
            array_push($menu_elements, $value);
        else {
			$menu_elements = add_into_array($menu_elements, $value);
        }   
	}
}

// add navbar
if ($element != null)
    array_unshift($nav_elements, $element);
?>

<div class='nav'>
<?php 
foreach (array_reverse($nav_elements) as $key=>$value) {
    if ($key != count($nav_elements)-1)
        echo "<a href='?id=".$value['id']."'>".$value['ime']."</a> / ";
    else 
        echo $value['ime'];
}
?>
</div>



<div class='container'>
	<div class='menu'>

<?php 
// add main menu
print_items($menu_elements, $element);
?>

	</div>
</div>

<?php 

// recursion function to add childs element 
function add_into_array($arr, $value) {
	foreach($arr as $key => $item) {
		if ($item['id'] == $value['parent']) {
			if (!isset($arr[$key]['childs']))
				$arr[$key]['childs'] = [];
			array_push($arr[$key]['childs'], $value);
			break;
		} elseif (isset($arr[$key]['childs'])) {
			$arr[$key]['childs'] = add_into_array($arr[$key]['childs'], $value);
		}
	}
	return $arr;
}

// recursion function for print all levels
function print_items($arr, $element) {
	echo "<ul>";
	foreach($arr as $value) {
		if ($value['id'] == $element['id'])
			echo "<li class='active'>".$value['ime']."</li>";
		else 
			echo "<li><a href='?id=".$value['id']."'>".$value['ime']."</a></li>";
	
		if (isset($value['childs'])) {
			print_items($value['childs'], $element);
		}
	}
	echo "</ul>";
}

?>


</body>
</html>