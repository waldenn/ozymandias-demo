<?php

$letter = 'A';

// Show search
if (isset($_GET['letter']))
{	
	$letter = $_GET['letter'];
}

?>

<html>
<head>
	<meta charset="utf-8">
    <script src="external/jquery.js"></script>
     
	<style>
		body {
			padding-left:40px;
			padding-right:40px;
			font-family: sans-serif;
		}
		
       .tab {
			padding:10px;
			display:block;
			float:left;
			border-right:1px solid rgb(192,192,192);
		}	
		
		.tab-active {
			padding:10px;
			display:block;
			float:left;
			border-right:1px solid rgb(192,192,192);

			background: orange;			
		
		}	
		

	</style>
	
	<script>
// List authors by letter of last name
function authors_by_name(letter, element_id) {
	
		
	var query = `SELECT DISTINCT ?author ?name ?orcid ?rg WHERE
{ 
  ?work wdt:P6982 ?afd .
  {
    ?work wdt:P50 ?author .
    ?author rdfs:label ?name .
    FILTER (lang(?name) = 'en') .
    
    # Filter on those with last name begining with letter
    FILTER (regex(str(?name), "\\\\s+` + letter + `\\\\w+(-\\\\w+)?$")) .
    
    ?author wdt:P496 ?orcid .
    
    OPTIONAL {
      ?author wdt:P2038 ?rg .
     }
  }
}
ORDER BY ?name`;
  

	$.getJSON('https://query.wikidata.org/bigdata/namespace/wdq/sparql?query=' + encodeURIComponent(query),
		function(data){
			//alert(JSON.stringify(data ));
			
			console.log(JSON.stringify(data, null, 2));
						
			if (data.results.bindings.length > 0) {
				var html = '';
				
				html += '<table>';
				
				html += '<tr><th>Name</th><th>ORCID</th><th>Ozymandias</th><th>ResearchGate</th></tr>';
			
				for (var i in data.results.bindings) {									
					
						html += '<tr>';
													
							html += '<td>'
								+ data.results.bindings[i].name.value 
								+ '</td>';
								
							html += '<td>'
								+ '<img src="images/orcid_16x16.png">'
								+ ' '
								+ '<a href="https://orcid.org/' + data.results.bindings[i].orcid.value + '" target="_new">'
								+ data.results.bindings[i].orcid.value 
								+ '</a>'
								+ '</td>';	
								
							// clean name to mimic Ozymandias
							html += '<td>';
														
							var name_string = data.results.bindings[i].name.value;
							name_string = name_string.replace(/ü/g, 'u');
							name_string = name_string.replace(/é/g, 'e');
							
							name_string = name_string.replace(/\./g, '. ');
							
							var m = name_string.match(/^([A-Z])\w+(\s+[A-Z]\.(\s*[A-Z]\.)?)?\s+([A-Z]\w+(-\w+)?)$/);
							if (m) {
								
								
								// html += JSON.stringify(m);
								
								
								var oz = [];
								var n = m.length;
								
								if (m[1]) {
									oz.push(m[1].toLowerCase());
								}
								
								if (m[2]) {
									var middle = m[2];
									middle = middle.replace(/^\s+/, '');
									middle = middle.replace(/\.$/g, '');
									middle = middle.replace(/\.\s/g, '-');
									middle = middle.replace(/\s+/g, '');
									
									oz.push(middle.toLowerCase());
								}

								if (m[n-2]) {
									oz.push(m[n-2].toLowerCase());
								}
								
								var short_name = oz.join('-');
								
								
								
								html += '<a href="https://ozymandias-demo.herokuapp.com/?uri=https://biodiversity.org.au/afd/publication/%23creator/' + short_name + '" target="_new">'
									+ short_name 
									+ '</a>';
								
								
							
							}
							html += '</td>';
							
							// ResearchGate image
							if (data.results.bindings[i].rg) {
								html += '<td>'
								    + '<img align="middle" style="border:1px solid rgb(192,192,192);" width="48" src="rgimage.php?id=' + data.results.bindings[i].rg.value + '">'
									+ ' ' + data.results.bindings[i].rg.value 
									+ '</td>';	
							}						
						
						html += '</tr>';  

				}
				html += '</table>';  
							
				$('#' + element_id).html(html);
				
			}			
		}
	);	
}	
</script>	
	
	
	
</head>
<body>

	<h1>People publishing on Australian taxonomy</h1>
	
	<div>
		<!-- letters -->
		<div style="border:1px solid rgb(192,192,192);">
<?php

foreach (range('A', 'Z') as $char) 
{
	echo '<li class="';
	
	if ($char == $letter)
	{
		echo 'tab-active';
	}
	else
	{
		echo 'tab';
	}
	
	echo '"><a href="?letter=' . $char . '">' . $char . '</a></li>';
}
?>		
		<div style="clear:both;"></div>
		</div>
		
		
		
		<!-- results box -->		
		<div style="width:100%;min-height:100px;">
			<div style="padding:10px;" id="names"></div>
		</div>
		
	</div>

	
<?php

//if ($query != "")
{
?>
	<script>
	
	authors_by_name('<?php echo $letter; ?>', 'names');
	
	/*
	var name = '<?php echo $query; ?>';
	var element_id = 'names';
	$('#' + element_id).html('');
	
	$('#header').html(name);
	
	works_for_taxon_from_name(name, element_id);
	*/
	</script>
<?php
}	
?>

	
</body>
</html>

