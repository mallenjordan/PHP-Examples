	<?php
	/* 
		TO DO
	
		- This whole thing probably needs a re-write.
		  I can correct and make this whole process a lot smaller and better.
		  Not just that but also all these following changes.
	
		- Possibly a save button, javascript with hidden dev holding all data.
		
		- fixed mono font
		
		- contrasting foreground text color in comparison to the background
		
		- raw CSV output side needs to be corrected to <element>, <element>, etc.
		  currently there is a space between the elements and the comma
		  
		- two different output for the raw html table print.
		  one with current non-wrap, second tabulated format output.
		  
		- termanology print for the controls. Naming.
		  low/high count needs 'card low/high index' 
		  prefix/postfix needs 'card index prefix/postfix'
		  table cell break, rename to output table colums
		  fill zero qty, rename to pre-fill qty
		  
		- compelte change and separation from the style sheet
		  have three sections; controls, input, output
		  . controls floating div, static to page scroll
		  . input and output should scroll together
		  
	
	*/
	
	// Gather External Variables
	$COUNT_LOW = ((isset($_POST["LOW"])) ? $_POST["LOW"] : 1);
	$COUNT_HIGH = ((isset($_POST["HIGH"])) ? $_POST["HIGH"] : 10);
	$NUMERIC_PRE = ((isset($_POST["PRE"])) ? $_POST["PRE"] : "");
	$NUMERIC_POST = ((isset($_POST["POST"])) ? $_POST["POST"] : "");
	$PREFILLER = ((isset($_POST["PREFILLER"])) ? $_POST["PREFILLER"] : -1);
	
	// overflow table break, breaks on this qty of cells.
	$OTB = ((isset($_POST["OTB"])) ? $_POST["OTB"] : 1);
	
	// Initialize Variables
	unset ($ARRAY);
	$TOTAL = 0;
	$BOOL = 1;
	$OTP = 1;
	$INPUT_FORM = "<table width=\"100%\" cellpadding=\"5\" cellspacing=\"0\"><tr id=\"TOb\"><td align=\"right\">CARD</td><td>QTY</td><td>INFO</td></tr>";
	$OUTPUT_MAIN = "CARDS:<br>";
	$OUTPUT_MISSING = "MISSING:<br>";
	$MISSING_COUNT = 0;
	$OUTPUT_TABLE = "<table border=\"1\" cellpadding=\"5\">";
	
	// Main Process Loop
	for ($INDEX = $COUNT_LOW; $INDEX < ($COUNT_HIGH + 1); $INDEX++) {
		$ARRAY[$INDEX]["QTY"] = ((isset($_POST["IDN" . $INDEX])) ? $_POST["IDN" . $INDEX] : 0);
		$ARRAY[$INDEX]["INFO"] = ((isset($_POST["IDC" . $INDEX])) ? $_POST["IDC" . $INDEX] : "");
		if ($ARRAY[$INDEX]["QTY"] > 0) {
			$TOTAL += $ARRAY[$INDEX]["QTY"];
			
			$LINE= 	$NUMERIC_PRE . $INDEX . $NUMERIC_POST;
			if ($ARRAY[$INDEX]["INFO"] !== "") { $LINE .= " " . $ARRAY[$INDEX]["INFO"] . " "; }
			$LINE .= (($ARRAY[$INDEX]["QTY"] > 1) ? " (x" . $ARRAY[$INDEX]["QTY"] . ")" : "");
			
			// Testing here for table cell breaks on the settings
			if ($OTP == 1) { $OUTPUT_TABLE .= "<tr>"; }
			$OUTPUT_TABLE .= "<td align=\"left\">" . $LINE . "</td>";
			if (($OTP + 1) > $OTB) { $OUTPUT_TABLE .= "</tr>"; $OTP = 1; }
			else { $OTP += 1; }
			
			$OUTPUT_MAIN .= $LINE . ", ";
		}
		else {
			if ($PREFILLER >= 0) {
				$ARRAY[$INDEX]["QTY"] = $PREFILLER;
			}
			else {
				$MISSING_COUNT++;
				$OUTPUT_MISSING .=	$NUMERIC_PRE . $INDEX . $NUMERIC_POST . " " . $ARRAY[$INDEX]["INFO"] . ", ";
			}
		}
		
		$CEL_COLOR = (($BOOL) ? "TOa" : "TOb");
		$INPUT_FORM .= "<tr id=\"" . $CEL_COLOR . "\">";
		$INPUT_FORM .= "<td align=\"right\">" . $NUMERIC_PRE . $INDEX . $NUMERIC_POST . "</td>";
		$INPUT_FORM .= "<td><input id=\"TText\" type=\"text\" size=\"5\" name=\"IDN" . $INDEX . "\" value=\"" . $ARRAY[$INDEX]["QTY"] . "\"></td>";
		$INPUT_FORM .= "<td><input id=\"TText\" type=\"text\" size=\"40\" name=\"IDC" . $INDEX . "\" value=\"" . $ARRAY[$INDEX]["INFO"] . "\"></td>";
		$INPUT_FORM .= "</tr>";
		$BOOL = !$BOOL;
	}

	// Closing Process
	$INPUT_FORM .= "</table>";
	$OUTPUT_TABLE .= "</tr></table>";
	
?>
<html>
	<head>
		<title>Card Sorter v2.6</title>
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>
	<body id="TBody">
		<table cellpadding="15" width="100%">
			<form action="cards 2.6.php" method="post">
			<td valign="top" width="15%">	
				<table>
					<tr><td><input id="TInput" type="text" size="5" name="LOW" value="<?php print $COUNT_LOW; ?>"> Low Count</td></tr>
					<tr><td><input id="TInput" type="text" size="5" name="HIGH" value="<?php print $COUNT_HIGH; ?>"> High Count</td></tr>
					<td><input id="TInput" type="text" size="5" name="PRE" value="<?php print $NUMERIC_PRE; ?>"> Prefix</td></tr>
					<td><input id="TInput" type="text" size="5" name="POST" value="<?php print $NUMERIC_POST; ?>"> Postfix</td></tr>
					<td><input id="TInput" type="text" size="5" name="OTB" value="<?php print $OTB; ?>"> Table Cell Break</td></tr>
					<td><input id="TInput" type="text" size="5" name="PREFILLER" value="<?php print $PREFILLER; ?>"> Fill ZERO QTY</td></tr>				
					<td><input id="TSubmit" type="submit" value="UPDATE"></td>
				</table>
			</td>
			<td valign="top"><?php print $INPUT_FORM; ?></td>
			<td valign="top">
			<?php 
				print "Total (Card Qty): " . $TOTAL;
				print "<br><br>Total (Per Slot): " . ($COUNT_HIGH - $MISSING_COUNT);
				print "<br>Missing (Per Slot): " . $MISSING_COUNT;
				print "<br><br>" . $OUTPUT_MAIN;
				print "<br><br>" . $OUTPUT_MISSING;
				print "<br><br>" . $OUTPUT_TABLE;
				print "<br><br>Cards Included:<br>";
				for ($INDEX = $COUNT_LOW; $INDEX < ($COUNT_HIGH + 1); $INDEX++) { 
					if ($ARRAY[$INDEX]["QTY"] > 0) {
						print "Card " . $NUMERIC_PRE . $INDEX . $NUMERIC_POST . " " . $ARRAY[$INDEX]["INFO"];
						if ($ARRAY[$INDEX]["QTY"] > 1) {
							print " (x" . $ARRAY[$INDEX]["QTY"] . "qty)";
						}
						print "<br>";
					}
				}
				print "<br><br>TABLE:<br>" . htmlspecialchars($OUTPUT_TABLE); 
			?>
			</td>
			</form>
		</table>
	</body>
</html>