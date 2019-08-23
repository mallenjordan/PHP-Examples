	<?php
		
		// error printing for testing...
		//ini_set('display_errors', 1);
		//ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		
		// TO DO:
		// Write a javascript save button
		// 'downloads' a hidden div with text listing of everything printed.
		// Save to local (user option) text file
		
		
		class superVariable {
			
			public $NAME;
			public $VALUE;
			public $TYPE;	
			public $DEFAULT;
			public $LOW;
			public $HIGH;
			
			function __construct ($NAME = "", $VALUE = 0, $TYPE = 0, $DEFAULT = 0, $LOW = 0, $HIGH = 0, $AUTO_GET_POST = FALSE) {
				$this->init ($NAME, $VALUE, $TYPE, $DEFAULT, $LOW, $HIGH, $AUTO_GET_POST);
			}
			
			function init ($NAME = "", $VALUE = 0, $TYPE = 0, $DEFAULT = 0, $LOW = 0, $HIGH = 0, $AUTO_GET_POST = FALSE) {
				$this->NAME = $NAME;
				$this->VALUE = $VALUE;
				
				if (($this->NAME != "") && ($AUTO_GET_POST)) { $this->getPost($DEFAULT); }
				
				if (($TYPE >= 0) && ($TYPE <= 3)) { $this->TYPE = $TYPE; }
				else { $this->TYPE = 0; }
				
				$this->DEFAULT = $DEFAULT;
				
				$this->LOW = $LOW;
				$this->HIGH = $HIGH;
				
				$this->correctLowHigh();
				
				$this->correctValues();
			}
			
			function correctLowHigh () {
				if ($this->LOW != $this->HIGH) {
					if ($this->LOW > $this->HIGH) { 
						$TEMP = $this->LOW; 
						$this->LOW = $this->HIGH; 
						$this->HIGH = $TEMP; 
					}
				}
			}
			
			function correctValues () {
				switch ($this->TYPE) {
					case 0: // numeric
						$this->VALUE = preg_replace('/[^0-9]/', '', $this->VALUE);
						if (($this->VALUE < $this->LOW) || ($this->VALUE > $this->HIGH)) {
							$this->VALUE = $this->DEFAULT;
						}
						break;
					case 1: // string
						$this->VALUE = preg_replace('/[^a-zA-Z]/', '', $this->VALUE);
						break;
					case 2: // alpha and numeric mix
						$this->VALUE = preg_replace('/[^a-zA-Z0-9]/', '', $this->VALUE);
						break;
					case 3: // bool
						$this->VALUE = ((($this->VALUE == TRUE) || ($this->VALUE == FALSE)) ? $this->VALUE : FALSE);
						break;
				}
			}
			
			function getPost ($DEFAULT = 0) {
				$this->VALUE = getPostValue($this->NAME, $DEFAULT);
			}
			
		}
		
		function getPostValue ($NAME, $DEFAULT) {
			$RETURN = "";													// init that return variable..
			$RETURN = (isset($_POST[$NAME]) ? $_POST[$NAME] : $DEFAULT);	// get the post value, or default..
			$RETURN = trim($RETURN);										// trim the white spaces..
			$RETURN = strip_tags($RETURN); 									// Probably redundant but...
			$RETURN = filter_var($RETURN, FILTER_SANITIZE_STRING);			// filter strip the value string wise..
			if ($RETURN > 0) {												
				$RETURN = ltrim($RETURN, "0");								// trim the leading zeros, if any..
			}
			return $RETURN;
		}
		
		class cardStack {
			
			public $LOW;
			public $HIGH;
			public $PREFIX;
			public $POSTFIX;
			public $PREFILL;
			public $TABLE_COL;
			
			public $TOTAL_CARD_ROW;
			public $TOTAL_CARD_QTY;
			public $TOTAL_CARD_MISSING;
			
			public $OUTPUT_TABLE;
			public $OUTPUT_FORM;
			public $OUTPUT_CVS;
			public $OUTPUT_MISSING;
			
			public $ARRAY_QTY;
			public $ARRAY_QTY_NAME;
			public $ARRAY_DATA;
			public $ARRAY_DATA_NAME;
			public $ARRAY_MISSING;
			
			function __construct() { $this->init(); }
			
			function init () {
				
				// calling init/construct on superVariable:
				// Name, Value, Type, Default, Low, High, AUTO_GET_POST
				// (Type: 0 numeric, 1 string, 2 boo)
				
				//										Name		Value	Type	Default		Low		High	AUTO_GET_POST
				$this->LOW = new superVariable (		"LOW", 		1, 		0, 		1, 			0, 		999, 	TRUE);
				$this->HIGH = new superVariable (		"HIGH", 	10, 	0, 		10,			1, 		1000, 	TRUE);
				$this->PREFIX = new superVariable (		"PREFIX", 	"", 	2, 		"", 		0, 		1, 		TRUE);
				$this->POSTFIX = new superVariable (	"POSTFIX", 	"", 	2, 		"", 		0, 		1, 		TRUE);
				$this->PREFILL = new superVariable (	"PREFILL", 	0, 		0, 		0, 			0,		10000,	TRUE);
				$this->TABLE_COL = new superVariable (	"TABLECOL", 1, 		0, 		1, 			1, 		10, 	TRUE);
				
				$this->TOTAL_CARD_ROW = 0;
				$this->TOTAL_CARD_QTY = 0;
				$this->TOTAL_CARD_MISSING = 0;
				
				$this->OUTPUT_TABLE = "(Empty)";
				
				if (isset($this->OUTPUT_FORM)) { unset($this->OUTPUT_FORM); }
				if (isset($this->OUTPUT_CVS)) { unset($this->OUTPUT_CVS); }
				if (isset($this->OUTPUT_MISSING)) { unset($this->OUTPUT_MISSING); }
				if (isset($this->ARRAY_QTY)) { unset($this->ARRAY_QTY); }
				if (isset($this->ARRAY_DATA)) { unset($this->ARRAY_DATA); }
				if (isset($this->ARRAY_MISSING)) { unset($this->ARRAY_MISSING); }
				
				$this->ARRAY_QTY_NAME = "AQ";
				$this->ARRAY_DATA_NAME = "AD";
				
				for ($INDEX = $this->LOW->VALUE; $INDEX < ($this->HIGH->VALUE + 1); $INDEX++) {
					$this->ARRAY_QTY[$INDEX] = (($this->PREFILL->VALUE > 0) ? $this->PREFILL->VALUE : getPostValue($this->ARRAY_QTY_NAME . $INDEX, 0));
					$this->ARRAY_DATA[$INDEX] = getPostValue($this->ARRAY_DATA_NAME . $INDEX, "");
					
					if ($this->ARRAY_QTY[$INDEX] == 0) { $this->ARRAY_MISSING[(count($this->ARRAY_MISSING))] = $INDEX; }
					else {
						$this->TOTAL_CARD_ROW++;
						$this->TOTAL_CARD_QTY += $this->ARRAY_QTY[$INDEX];
					}
				}
				
				$this->PREFILL->VALUE = 0;
				
				$this->TOTAL_CARD_MISSING = count($this->ARRAY_MISSING);
				
				$this->buildPrintInputForm();
				$this->buildPrintOutputTable();
				
				$this->buildPrintOutputCVS();
				$this->buildPrintMissingCards();
			}
			
			function buildPrintMissingCards() {
				$BUFFER = "";
				$BREAK = FALSE;
				for ($INDEX = 0; $INDEX < count($this->ARRAY_MISSING); $INDEX++) {
					if ($BREAK) { $BUFFER .= ", "; }
					$BREAK = TRUE;
					
					$BUFFER .= $this->PREFIX->VALUE . $this->ARRAY_MISSING[$INDEX] . $this->POSTFIX->VALUE;
				}
				
				$this->OUTPUT_MISSING = $BUFFER;
			}
			
			function buildPrintOutputCVS () {
				$BUFFER = "";
				$BREAK = FALSE;
				for ($INDEX = $this->LOW->VALUE; $INDEX < ($this->HIGH->VALUE + 1); $INDEX++) {
					if ($this->ARRAY_QTY[$INDEX] > 0) {
						if ($BREAK) { $BUFFER .= ", "; }
						$BREAK = TRUE;
					
						$BUFFER .= $this->PREFIX->VALUE . $INDEX . $this->POSTFIX->VALUE;
						if ($this->ARRAY_DATA[$INDEX] != "") { $BUFFER .= " " . $this->ARRAY_DATA[$INDEX]; }
						if ($this->ARRAY_QTY[$INDEX] > 1) { $BUFFER .= " (x" . $this->ARRAY_QTY[$INDEX] . ")"; }
					}
				}
				
				$this->OUTPUT_CVS = $BUFFER;
			}
			
			function buildPrintInputForm () {
				$BUFFER = "";
				$BOOL = TRUE;
				
				$BUFFER .= "<table>";
				$BUFFER .= "<tr><td colspan=\"3\" id=\"HeaderB\">INPUT</td></tr>";
				for ($INDEX = $this->LOW->VALUE; $INDEX < ($this->HIGH->VALUE + 1); $INDEX++) {
						$BUFFER .= "<tr ";
						$BUFFER .= " id=\"" . (($BOOL) ? "bgA" : "bgB") . "\">";
						$BOOL = !$BOOL;
						
							$BUFFER .= "<td width=\"25px\" align=\"right\" style=\"padding: 0px 15px 0px 10px;\">";
							$BUFFER .= $this->PREFIX->VALUE . $INDEX . $this->POSTFIX->VALUE;
							$BUFFER .= "</td>";
							
							$BUFFER .= "<td>";
							$BUFFER .= "<input type=\"text\" size=\"5\" name=\"" . $this->ARRAY_QTY_NAME . $INDEX . "\" value=\"" . $this->ARRAY_QTY[$INDEX] . "\">";
							$BUFFER .= "</td>";
							
							$BUFFER .= "<td>";
							$BUFFER .= "<input type=\"text\" size=\"40\" name=\"" . $this->ARRAY_DATA_NAME . $INDEX . "\" value=\"" . $this->ARRAY_DATA[$INDEX] . "\">";
							$BUFFER .= "</td>";
							
						$BUFFER .= "</tr>";
				}
				$BUFFER .= "</table>";
				
				$this->OUTPUT_FORM = $BUFFER;
			}
			
			function buildPrintOutputTable () {
				$BUILD_BREAK = 1;
				$BUFFER = "";
				$BUFFER = "<table>\n";
				for ($INDEX = $this->LOW->VALUE; $INDEX < ($this->HIGH->VALUE + 1); $INDEX++) {
					if ($this->ARRAY_QTY[$INDEX] > 0) {						
						if ($BUILD_BREAK == 1) { $BUFFER .= "\t<tr>"; }
						
						$BUFFER .= "<td>" . $this->getLine($INDEX) . "</td>";
						
						if (($BUILD_BREAK + 1) > $this->TABLE_COL->VALUE) { $BUFFER .= "</tr>\n"; $BUILD_BREAK = 1; }
						else { $BUILD_BREAK++; }
					}
				}
				$BUFFER .= "</table>";
				
				if ($BUFFER !== "<table>\n</table>") { $this->OUTPUT_TABLE = $BUFFER; }
			}
			
			function getLine ($INDEX) {
				$LINE = "";
				$LINE = $this->PREFIX->VALUE . $INDEX . $this->POSTFIX->VALUE;
				if ($this->ARRAY_DATA[$INDEX] !== "") { $LINE .= " " . $this->ARRAY_DATA[$INDEX]; }
				if ($this->ARRAY_QTY[$INDEX] > 1) { $LINE .= " (x" . $this->ARRAY_QTY[$INDEX] . ")"; }
				
				return $LINE;
			}
		}
		
		$VERSION = "2.8";
		
		$CARDS = new cardStack();
	?>
	<html>
		<head>
			<title>Card Sorter v<?php print $VERSION; ?></title>
			<link rel="stylesheet" type="text/css" href="cardsstyle.css">
		</head>
		<body>
			<?php
				//print "<pre>"; print_r($CARDS); print "</pre><br>";
			?>
			<table cellpadding="15" width="100%">
				<form action="<?php print basename($_SERVER["SCRIPT_FILENAME"]); ?>" method="post">
				<td valign="top" width="33%">	
					<table width="100%">
						<tr><td colspan="2" id="HeaderA">CONTROL</td></tr>
						<tr><td><input type="text" size="5" name="<?php print $CARDS->LOW->NAME; ?>" value="<?php print $CARDS->LOW->VALUE; ?>"></td><td>Low Count f(0~1k) ; [numeric]</td></tr>
						<tr><td><input type="text" size="5" name="<?php print $CARDS->HIGH->NAME; ?>" value="<?php print $CARDS->HIGH->VALUE; ?>"></td><td>High Count f(1~1k) ; [numeric]</td></tr>
						<tr><td><br></td></tr>
						<tr><td><input type="text" size="5" name="<?php print $CARDS->PREFIX->NAME; ?>" value="<?php print $CARDS->PREFIX->VALUE; ?>"></td><td>Card Prefix ; [alpha-numeric]</td></tr>
						<tr><td><input type="text" size="5" name="<?php print $CARDS->POSTFIX->NAME; ?>" value="<?php print $CARDS->POSTFIX->VALUE; ?>"></td><td>Card Postfix ; [alpha-numeric]</td></tr>
						<tr><td><br></td></tr>
						<tr><td><input type="text" size="5" name="<?php print $CARDS->TABLE_COL->NAME; ?>" value="<?php print $CARDS->TABLE_COL->VALUE; ?>"></td><td>Table Colums f(1~10) ; [numeric]</td></tr>
						<tr><td><br></td></tr>
						<tr><td><input type="text" size="5" name="<?php print $CARDS->PREFILL->NAME; ?>" value="<?php print $CARDS->PREFILL->VALUE; ?>"></td><td>Prefill Qty f(0~10k) ; [numeric]</td></tr>
						<tr><td colspan="2"><br>Note:<br>Anything numeric value of 1(one)<br>or greater will reset the whole<br>array qty.</td></tr>				
						<tr><td><br></td></tr>
						<tr><td colspan="2"><input type="submit" value="UPDATE"></td>
					</table>
				</td>
				<td valign="top" width="33%"><?php print $CARDS->OUTPUT_FORM; ?></td>
				<td valign="top">
					<table width="100%">
						<tr><td id="HeaderC">OUTPUT</td></tr>
						<tr><td><?php 
									print "Total (Card Per Qty): " . $CARDS->TOTAL_CARD_QTY;
									print "<br><br>Total (Card Per Row): " . $CARDS->TOTAL_CARD_ROW;
									print "<br><br>Missing (Card Per Row): " . $CARDS->TOTAL_CARD_MISSING;
									print "<br><br>Cards (CVS):<br>" . $CARDS->OUTPUT_CVS;
									print "<br><br>Missing (CVS):<br>" . $CARDS->OUTPUT_MISSING;
									print "<br><br>Physical Table [" . $CARDS->TABLE_COL->VALUE . "  Column(s)]:<br><br>" . $CARDS->OUTPUT_TABLE;
									print "<br><br>HTML Table:<pre>" . htmlspecialchars($CARDS->OUTPUT_TABLE) . "</pre>"; 
								?>
						</td></tr>
				</td>
				</form>
			</table>
		</body>
	</html>
