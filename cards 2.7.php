<?php
	// Later: Write a javascript save button, 'downloads' a hidden div with text listing of everything printed.
	
	
	function getPostVal ($stringName, $autoDefault) {
		return ((isset($_POST[$stringName])) ? $_POST[$stringName] : $autoDefault);
	}
	
	function correctValue ($intValue, $intBottom, $intTop, $intDefault) {
		if (($intValue >= $intBottom) && ($intValue <= $intTop)) { return $intValue; }
		else { return $intDefault; }
	}
	
	$VERSION = "2.8";
	
	// Gather External Variables
	$COUNT_LOW = correctValue(getPostVal("LOW", 1), 0, 100, 1);
	$COUNT_HIGH = correctValue(getPostVal("HIGH", 10), 0, 1000, 10);
	$NUMERIC_PRE = getPostVal("PRE", "");
	$NUMERIC_POST = getPostVal("POST", "");
	$PREFILLER = correctValue(getPostVal("PREFILLER", -1), -1, 1000, -1);
	
	// overflow table break, breaks on this qty of cells.
	$OTB = correctValue(getPostVal("OTB", 1), 1, 10, 1);
	
	// Initialize Variables
	unset ($ARRAY);
	$TOTAL = 0;
	$BOOL = 1;
	$OTP = 1;
	$INPUT_FORM = "<table width=\"100%\" cellpadding=\"5\" cellspacing=\"0\"><tr id=\"TOb\"><td id=\"IPForm\" align=\"right\">CARD</td><td id=\"IPForm\">QTY</td><td id=\"IPForm\">INFO</td></tr>";
	$OUTPUT_MAIN = "Cards (CVS Text):<br>";
	$OUTPUT_MISSING = "MISSING:<br>";
	$MISSING_COUNT = 0;
	$OUTPUT_TABLE = "<table>\n";
	
	// New additions
	$boolOutputMain = false;
	$boolOutputMissing = false;
	
	// Main Process Loop
	for ($INDEX = $COUNT_LOW; $INDEX < ($COUNT_HIGH + 1); $INDEX++) {
		$ARRAY[$INDEX]["QTY"] = getPostVal("IDN" . $INDEX, 0);
		$ARRAY[$INDEX]["INFO"] = getPostVal("IDC" . $INDEX, "");
		if ($ARRAY[$INDEX]["QTY"] > 0) {
			$TOTAL += $ARRAY[$INDEX]["QTY"];
			
			if ($boolOutputMain) { $OUTPUT_MAIN .= ", "; }
			$boolOutputMain = true;
			$LINE= 	$NUMERIC_PRE . $INDEX . $NUMERIC_POST;
			if ($ARRAY[$INDEX]["INFO"] !== "") { $LINE .= " " . $ARRAY[$INDEX]["INFO"]; }
			$LINE .= (($ARRAY[$INDEX]["QTY"] > 1) ? " (x" . $ARRAY[$INDEX]["QTY"] . ")" : "");
			$OUTPUT_MAIN .= $LINE;
			
			// Testing here for table cell breaks on the settings
			if ($OTP == 1) { $OUTPUT_TABLE .= "\t<tr>"; }
			$OUTPUT_TABLE .= "<td>" . $LINE . "</td>";
			if (($OTP + 1) > $OTB) { $OUTPUT_TABLE .= "</tr>\n"; $OTP = 1; }
			else { $OTP += 1; }
		}
		else {
			if ($PREFILLER >= 0) {
				$ARRAY[$INDEX]["QTY"] = $PREFILLER;
			}
			else {
				$MISSING_COUNT++;
				if ($boolOutputMissing) { $OUTPUT_MISSING .= ", "; }
				$boolOutputMissing = true;
				$OUTPUT_MISSING .=	$NUMERIC_PRE . $INDEX . $NUMERIC_POST;
				if ($ARRAY[$INDEX]["INFO"] != "") { $OUTPUT_MISSING .= " " . $ARRAY[$INDEX]["INFO"]; }
			}
		}
		
		$CEL_COLOR = (($BOOL) ? "TOa" : "TOb");
		$INPUT_FORM .= "<tr id=\"" . $CEL_COLOR . "\">";
		$INPUT_FORM .= "<td id=\"IPForm\" align=\"right\">" . $NUMERIC_PRE . $INDEX . $NUMERIC_POST . "</td>";
		$INPUT_FORM .= "<td id=\"IPForm\"><input id=\"TText\" type=\"text\" size=\"5\" name=\"IDN" . $INDEX . "\" value=\"" . $ARRAY[$INDEX]["QTY"] . "\"></td>";
		$INPUT_FORM .= "<td id=\"IPForm\"><input id=\"TText\" type=\"text\" size=\"40\" name=\"IDC" . $INDEX . "\" value=\"" . $ARRAY[$INDEX]["INFO"] . "\"></td>";
		$INPUT_FORM .= "</tr>";
		$BOOL = !$BOOL;
	}

	// Closing Process
	$INPUT_FORM .= "</table>";
	$OUTPUT_TABLE .= "</table>";
	
?>
<html>
	<head>
		<title>Card Sorter v<?php print $VERSION; ?></title>
		<link rel="stylesheet" type="text/css" href="cardsstyle.css">
	</head>
	<body>
		<table cellpadding="15" width="100%">
			<form action="cards 2.7.php" method="post">
			<td valign="top" width="20%">	
				<table width="100%">
					<tr><td><input id="TInput" type="text" size="5" name="LOW" value="<?php print $COUNT_LOW; ?>"> Low Count f(0~1k)</td></tr>
					<tr><td><input id="TInput" type="text" size="5" name="HIGH" value="<?php print $COUNT_HIGH; ?>"> High Count f(1~1k)</td></tr>
					<tr><td><input id="TInput" type="text" size="5" name="PRE" value="<?php print $NUMERIC_PRE; ?>"> Card Prefix</td></tr>
					<tr><td><input id="TInput" type="text" size="5" name="POST" value="<?php print $NUMERIC_POST; ?>"> Card Postfix</td></tr>
					<tr><td><input id="TInput" type="text" size="5" name="OTB" value="<?php print $OTB; ?>"> Table Colums f(1~10)</td></tr>
					<tr><td><input id="TInput" type="text" size="5" name="PREFILLER" value="<?php print $PREFILLER; ?>"> Prefill Qty (-1)</td></tr>				
					<tr><td><input id="TSubmit" type="submit" value="UPDATE"></td>
				</table>
			</td>
			<td valign="top"><?php print $INPUT_FORM; ?></td>
			<td valign="top">
			<?php 
				print "Total (Card Qty): " . $TOTAL;
				print "<br><br>Total (Per Card Row): " . ($COUNT_HIGH - $MISSING_COUNT);
				print "<br>Missing (Per Card Row): " . $MISSING_COUNT;
				print "<br><br>" . $OUTPUT_MAIN;
				print "<br><br>" . $OUTPUT_MISSING;
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
				print "<br><br>Physical Table [" . $OTB . "  Column(s)]:<br>" . $OUTPUT_TABLE;
				print "<br><br>HTML Table:<pre>" . htmlspecialchars($OUTPUT_TABLE) . "</pre>"; 
				
			?>
			</td>
			</form>
		</table>
	</body>
</html>