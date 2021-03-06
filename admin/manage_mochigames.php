<?php
include_once("mochi_functions.php");
if (isset($_GET['feed'])) {
	get_mochifeed();
}
?>
<div id="center-column">
<div class="top-bar">
<h1><?php $translate->__('Install Mochimedia.com Feed Games');?></h1>
<div class="breadcrumbs"><a href="index.php?act=mochiid" title="<?php $translate->__('Update Mochi ID');?>"><?php $translate->__('Update Mochi ID');?></a></div>
</div><br />
<div class="select-bar"></div>
<?php
$result = yasDB_select("SELECT COUNT(id) FROM mochigames WHERE hidden = 0"); 
$query_data = $result->fetch_array(MYSQLI_NUM);
$numrows = $query_data[0];
$result->close();
$result2 = yasDB_select("SELECT `sourceid` FROM `gameque` WHERE `source` = 'mochi'");
$queids = array();
while ($source = $result2->fetch_array(MYSQLI_NUM)) {
	$queids[] = $source[0];
}
?>
<script type="text/javascript">
		$(document).ready(function() {
			$("#game").fancybox({
				'type'              : 'swf',
				'padding'			: 0,
				'autoScale'			: true,
				'transitionIn'		: 'elastic',
				'transitionOut'		: 'elastic'
			});
		});
</script>
<center>
<form id="fgdfeed" action="index.php" method="get">
	<input type="hidden" name="act" value="managemochi">
	<input type="submit" class="button" name="feed" value="Get Feed" onClick="javascript: getMochiFeed(); return false;"/>
</form>
<div id="preview"></div>
	<h4><?php $translate->__('Note: Installing a lot of games at once is resource intensive.');?></h4>
    <p align="center">  
    <form enctype="multipart/form-data" action="index.php" method="get">
    	<input type="hidden" name="act" value="managemochi">
		<label for="category_filter"><?php $translate->__('Category');?>:</label>
        <select name="category" id="category">
        	<option value="all" selected>All Categories</option>
        	<optgroup label="category">
				<option value="action">Action</option>
				<option value="adventure">Adventure</option>
				<option value="board_game">Board Game</option>
				<option value="casino">Casino</option>
				<option value="dress-up">Dress up</option>
				<option value="driving">Driving</option>
				<option value="education">Education</option>
				<option value="fighting">Fighting</option>
				<option value="jigsaw">Jigsaw / Slider Puzzles</option>
				<option value="other">Other</option>
				<option value="customize">Pimp my / Customize</option>
				<option value="puzzles">Puzzles</option>
				<option value="shooting">Shooting</option>
				<option value="rhythm">Rhythm</option>
				<option value="sports">Sports</option>
				<option value="strategy">Strategy</option>
           	</optgroup>
           	<optgroup label="other">
          		<option value="recommended">Featured Games</option>
           		<option value="coins">Coin Enabled</option>
           		<option value="leaderboard">Leaderboard Enabled</option>
           	</optgroup>
        </select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <label for="rating_filter"><?php $translate->__('Rating');?>:</label>
        <select id="rating" name="rating">
        	<option value="all" selected>All Ratings</option>
        	<option value="everyone">Everyone</option>
        	<option value="teen">Teen</option>
        	<option value="mature">Mature</option>
        </select><br/><br/>                        		
        <?php $translate->__('Description');?>:<input type="text" name="description"/>
        <?php $translate->__('Keywords');?>:<input type="text" name="keywords"/>
		</p><br/>
		<input type="submit" class="button" name="filter" value="Filter Games" />
    </form>
</center>
<div style="position:relative;float:right;margin-bottom:5px;">
<form action="index.php" method="get">                               	 
	<input type="hidden" name="act" value="mochiall"/>
	<input type="submit" class="button" name="install" value="Install Filtered List"/></center>
	<input type="hidden" name = "category" value="<?php if (isset($_GET['category'])) {echo $_GET['category'];} else {echo '';} ?>"/>	
	<input type="hidden" name= "rating" value ="<?php if (isset($_GET['rating'])) {echo $_GET['rating'];} else {echo '';} ?>"/>
	<input type="hidden" name="keywords" value ="<?php if (isset($_GET['keywords'])) {echo $_GET['keywords'];} else {echo '';} ?>"/>
	<input type="hidden" name = "description" value="<?php if (isset($_GET['description'])) {echo $_GET['description'];} else {echo '';} ?>"/>
</form>
</div>
	<?php
	if (!isset($_GET['category']) || empty($_GET['category'])) {
		$cat = 'all';
	} else {
		$cat = $_GET['category'];
	}
	if (!isset($_GET['rating']) || empty($_GET['rating'])) {
		$rat = 'all';
	} else {
		$rat = $_GET['rating'];
	}
	if (!isset($_GET['description']) || empty($_GET['description'])) {
		$des = '---';
	} else {
		$des = $_GET['description'];
	}
	if (!isset($_GET['keywords']) || empty($_GET['keywords'])) {
		$key = '---';
	} else {
		$key = $_GET['keywords'];
	}
	//      Number of games       //
	if (!empty($_GET['category'])) {
		$category = $_GET['category'];
		$rating = $_GET['rating'];
		$description = yasDB_clean($_GET['description']);
		$keywords = yasDB_clean($_GET['keywords']);
	} else {
		$category ='all';
		$rating = 'all';
		$description = '';
		$keywords = '';
	}
	if ($rating == 'all') {
		$sql_rating = '';
	} else {
		$sql_rating = " rating = '$rating' AND";
	}
	if ($category == 'coins') {
		$sql_category = ' coinsenabled = 1 AND';
	} elseif ($category == 'leaderboard'){
		$sql_category = ' leaderboard = 1 AND';
	} elseif ($category == 'recommended') {
		$sql_category = ' recommended = 1 AND';
	} elseif ($category == 'all') {
		$sql_category = '';
	} else {
		$sql_category = " categories LIKE '%$category%' AND";
	}
	if ($keywords == '') {
		$sql_keywords = '';
	} else {
		$sql_keywords = " keywords LIKE '%$keywords%' AND";
	}
	if ($description == '') {
		$sql_description = '';
	} else {
		$sql_description = " description LIKE '%$description%' AND";
	}
	$sql = 'SELECT count(id) FROM mochigames WHERE' . $sql_rating . $sql_category . $sql_keywords . $sql_description . ' isinstalled = 0 AND hidden = 0';
	$query = yasDB_select($sql,false);	
	$num_games = $query->fetch_array(MYSQLI_NUM);	
	$query->close();
	
	echo '<table class="listing" cellpadding="0" cellspacing="0">
	    <tr style="font-weight: bold;">
	    <td width="100px">
		Category: '.$cat.'</td>
		<td align="center">Rating: '.$rat.'</td>
		<td align="center">Description: '.$des.'</td>
		<td align="center">Keywords: '.$key.'</td>
		<td class="last" align="center">Games: '.$num_games[0].'</td>
		</tr></table>';
	flush2();
	if (isset($_GET['page'])) {
	   $pageno = $_GET['page'];
	} else {
	   $pageno = 1;
	} 
	
//**********************************************************************************************************************************************			
	if (isset($_GET['filter'])) {
		$category = $_GET['category'];
		$rating = $_GET['rating']; 
		$description = yasDB_clean($_GET['description']);
		$keywords = yasDB_clean($_GET['keywords']);
	} else {
		$category = "all";
		$rating = "all";
		$description = '';
		$keywords = '';
	}
	if ($rating == 'all') {
		$sql_rating = '';
	} else {
		$sql_rating = " rating = '$rating' AND";
	}
	if ($category == 'coins') {
		$sql_category = ' coinsenabled = 1 AND';
	} elseif ($category == 'leaderboard'){
		$sql_category = ' leaderboard = 1 AND';
	} elseif ($category == 'recommended') {
		$sql_category = ' recommended = 1 AND';
	} elseif ($category == 'all') {
		$sql_category = '';
	} else {
		$sql_category = " categories LIKE '%$category%' AND";
	}
	if ($keywords == '') {
		$sql_keywords = '';
	} else {
		$sql_keywords = " keywords LIKE '%$keywords%' AND";
	}
	if ($description == '') {
		$sql_description = '';
	} else {
		$sql_description = " description LIKE '%$description%' AND";
	}
	$sql = 'SELECT COUNT(id) FROM mochigames WHERE' . $sql_rating . $sql_category . $sql_keywords . $sql_description . ' isinstalled = 0 AND hidden = 0';	
	$result = yasDB_select($sql,false);
	$query_data = $result->fetch_array(MYSQLI_NUM);
	$numrows = $query_data[0];
	$result->close();
	
	$rows_per_page = 20;
	$lastpage = ceil($numrows/$rows_per_page);
	if($lastpage<1) {
		$lastpage = 1;
	}
	$pageno = (int)$pageno;
	if ($pageno < 1) {
	   $pageno = 1;
	} 
	elseif ($pageno > $lastpage) {
	   $pageno = $lastpage;
	} 
	$limit = 'LIMIT ' .($pageno - 1) * $rows_per_page .',' .$rows_per_page;
	?>
	<div class="table">
		<table class="listing" cellpadding="0" cellspacing="0">
			<thead>
			<tr>
				<th width="90px"><?php $translate->__('Title');?></th>
				<th><?php $translate->__('Categories');?></th>
				<th><?php $translate->__('Rating');?></th>
				<th><?php $translate->__('Description');?></th>
				<th><?php $translate->__('Keywords');?></th>
				<th><?php $translate->__('Leaderboard');?></th>
				<th><?php $translate->__('Install');?></th>
			</tr>
			</thead>
			<?php
//*************************************************************************************************************************************************	
		$sql = 'SELECT * FROM mochigames WHERE' . $sql_rating . $sql_category . $sql_keywords . $sql_description . ' isinstalled = 0 AND hidden = 0 ORDER BY id DESC '.$limit;
		$query = yasDB_select($sql,false);
	if($query->num_rows == 0) {
    	echo '<center><b style="color: #7F7F7F">No games meet your criteria.</b></center>';
	}
	else {                         
    	$i=0;
		while($row = $query->fetch_array(MYSQLI_ASSOC)) {
			$i++;
?>			 
		<script type="text/javascript">
		$(document).ready(function() {
			$("#game<?php echo $i;?>").fancybox({
				'type'              : 'swf',
				'padding'			: 0,
				'autoScale'			: true,
				'transitionIn'		: 'elastic',
				'transitionOut'		: 'elastic'
				
			});
		});
		</script>
<?php
			if ($row['isinstalled'] != 1) {
				$thumburl = $row['thumburl'];
				$desc = substr($row['description'], 0, 75);   // Limit the size of description displayed
				//$instr = substr($row['instructions'], 0, 75); 
				$keys = substr($row['keywords'], 0, 75);
				$gameid = $row['id'];
				if ($row['leaderboard'] == 1) {
						 $leaderboard = 'Yes';
				}
				else {
					$leaderboard = '';
				}
				$gameurl = $row['gameurl'];
				echo '<tr>
			<td class="first style1" width="80px"><img src="'.$thumburl.'" style="width: 60px; height: 60px;" /><br/>' . $row['name'] . '</td>
			<td>' . $row['categories'] . '</td>
			<td>' . $row['rating'] . '</td>
			<td style="overflow:hidden;">' . $desc . '</td>
			<td>' . $keys . '</td>
			<td>';
			if ($leaderboard == 'Yes') {
				echo '<img src="img/checkmark.png" width="16" height="16" alt="Yes" />';
			}			
			echo '</td>
			<td><form action="index.php" method="get">
			<input type="hidden" name="act" value="managemochi"/>
			<input type="submit" class="button" name="install" value="Install" /><br/><br/>';
			if (!in_array($gameid, $queids)) {
				echo '<div id="dummy'.$gameid.'"><input type="button" id="qbutton'.$gameid.'" class="button" name="que" value="Queue it" onclick="return doQue('.$gameid.', \'que\', \'mochi\', \''. $row['name'].'\', \''.$thumburl.'\')"/></div><br/><br/>';
			} else {
				echo '<div id="dummy'.$gameid.'"><input type="button" id="qbutton'.$gameid.'" class="button_remove" name="que" value="UN-queue" style="font-size:11px;" onclick="return doQue('.$gameid.', \'remove\', \'mochi\', \''. $row['name'].'\', \''.$thumburl.'\')"/></div><br/>';
			}
			echo '<a id = "game'.$i.'" href="'.$gameurl.'" target="_blank" alt="'.$row['name'].'">Try it!</a>
			<input type="hidden" name="gameid" value="'.$gameid.'" />
			<input type="hidden" name="page" value="'.$pageno.'" />
			<input type="hidden" name = "category" value="'.$category.'"/>	
			<input type="hidden" name="rating" value ="'.$rating.'"/>
			<input type="hidden" name = "description" value="'.$description.'"/>	
			<input type="hidden" name="keywords" value ="'.$keywords.'"/>					
			</form></td>
			</tr>';
			}
		}
	}
	$query->close();
	
	if (isset($_GET['gamepage'])) {
		$pageno = $_GET['gamepage'];
	}
	?>
		<tfoot>
		<tr>
			<th width="90px"><?php $translate->__('Title');?></th>
			<th><?php $translate->__('Categories');?></th>
			<th><?php $translate->__('Rating');?></th>
			<th><?php $translate->__('Description');?></th>
			<th><?php $translate->__('Keywords');?></th>
			<th><?php $translate->__('Leaderboard');?></th>
			<th><?php $translate->__('Install');?></th>
		</tr>
		</tfoot>
	</table></div><br /><div style="text-align:center;">
	<?php
	if ($pageno == 1) {
	   echo ' FIRST PREV ';
	} else {
	   echo ' <a href="index.php?act=managemochi&page=1&category='.$category.'&rating='.$rating.'&description='.$description.'&keywords='.$keywords.'&filter=Filter games">FIRST</a>';
	   $prevpage = $pageno-1;
	   echo ' <a href="index.php?act=managemochi&page=' . $prevpage . '&category='.$category.'&rating='.$rating.'&description='.$description.'&keywords='.$keywords.'&filter=Filter games">PREV</a>';
	} 
	echo ' ( Page ' . $pageno . ' of ' . $lastpage . ' ) ';
	if ($pageno == $lastpage) {
	   echo ' NEXT LAST ';
	} else {
	   $nextpage = $pageno+1;
	   echo ' <a href="index.php?act=managemochi&page=' . $nextpage . '&category='.$category.'&rating='.$rating.'&description='.$description.'&keywords='.$keywords.'&filter=Filter games">NEXT</a>';
	   echo ' <a href="index.php?act=managemochi&page=' . $lastpage . '&category='.$category.'&rating='.$rating.'&description='.$description.'&keywords='.$keywords.'&filter=Filter games">LAST</a>';
	} 
	echo '</div>';
	if (isset($_GET['install'])) {
		if ($_GET['install'] == 'Install') {
			install_mochigame($_GET['gameid']) or die("Game did not install successfully");
			echo '<script>alert("Game sucessfully installed.");</script>';
			if (isset($_GET['page'])) {
	   			$pageno = $_GET['page'];
			}
			else {
	   			$pageno = 1;
			} 
			if (isset($_GET['category'])) {
	   			$category = $_GET['category'];
			}
			else {
	   			$category = 'all';
			}
			if (isset($_GET['rating'])) {
	   			$rating = $_GET['rating'];
			} else {
	   			$rating = 'all';
			} 
			$description = yasDB_clean($_GET['description']);
			$keywords = yasDB_clean($_GET['keywords']);
			echo '<META HTTP-EQUIV="Refresh" CONTENT="0; URL=index.php?act=managemochi&page='.$pageno.'&category='.$category.'&rating='.$rating.'&description='.$description.'&keywords='.$keywords.'&filter=Filter games;">';
			exit();
		}
		
	}
?>
</div>                      