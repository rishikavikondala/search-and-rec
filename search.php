<html>
<head>
	<title>Search</title>
<style>
  * {box-sizing: border-box; font-family: "Arial";}
  .column {float: left;}
  .search {width:70%;}
  .rec {width: 30%;}
  .interactive:after {content: ""; display: table; clear: both;}
  .footer {position: fixed; left: 0; bottom: 0; width: 100%; background-color: #4B2E83; color: #D7DBDD; text-align: center;}
</style>
</head>

<body style="padding: 25px 100px 25px 100px">

<h1>Search: -------</h1>
<p>About content in search...<br>
  Try searching <i>club</i>, <i>cse</i>, <i>engineering</i>, <i>major</i>, etc.</p>
<!-- LOADING ICON LIBRARY FOR SEARCH ICON -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<div class="interactive">
<div class="column search">
<form action="search.php" method="post">
	<input type="text" size=40 name="search_string" placeholder="Search..." value="<?php echo $_POST["search_string"];?>"/>
	<button id="request" type="submit"><i class="fa fa-search"></i></button>
</form>

<?php
	if (isset($_POST["search_string"])) {
		$search_string = $_POST["search_string"];
		$qfile = fopen("query.py", "w");

		fwrite($qfile, "import pyterrier as pt\nif not pt.started():\n\tpt.init()\n\n");
		fwrite($qfile, "import pandas as pd\nqueries = pd.DataFrame([[\"q1\", \"$search_string\"]], columns=[\"qid\",\"query\"])\n");
		fwrite($qfile, "index = pt.IndexFactory.of(\"./reddit_index/\")\n");
		fwrite($qfile, "tf_idf = pt.BatchRetrieve(index, wmodel=\"Hiemstra_LM\")\n");
		fwrite($qfile, "results = tf_idf.transform(queries)\n");

		for ($i=0; $i<10; $i++) {
			fwrite($qfile, "print(index.getMetaIndex().getItem(\"filename\",results.docid[$i])[:-10])\n");
			fwrite($qfile, "if index.getMetaIndex().getItem(\"title\", results.docid[$i]).strip() != \"\":\n");
			fwrite($qfile, "\tprint(index.getMetaIndex().getItem(\"title\",results.docid[$i]))\n");
			fwrite($qfile, "else:\n\tprint(index.getMetaIndex().getItem(\"filename\",results.docid[$i])[:-10])\n");
   		}

   		fclose($qfile);

   		exec("ls | nc -u 127.0.0.1 10005");
   		sleep(3);

   		$stream = fopen("output", "r");

   		$line=fgets($stream);

   		while(($line=fgets($stream))!=false) {
   			$clean_line = preg_replace('/\s+/','',$line);
   			$record = explode("./", $clean_line);
   			$line = fgets($stream);
   			echo "<a href=\"http://$record[1]\">".$line."</a><br/>\n";
   		}

   		fclose($stream);

   		exec("rm query.py");
   		exec("rm output");
   		}
?>
</div>
<div class="column rec">
  <p>Recent Searches</p>
  <ul>
    <!--echo `name of query`-->
    <!--inside while add: $query = gets($stream); then onclick=addsearch($query) -->
    <li onclick="addSearch(`Search 1`)">Search 1</li>
    <li onclick="addSearch(`club`)">Search 2</li>
    <li>Search 3</li>
  </ul>
  <script>
    function addSearch(popQuery) {
    document.getElementsByName("search_string")[0].value = popQuery;
    document.getElementById("request").form.submit();
    }
  </script>
</div>
</div>
<div class="footer">
  <p>Created by Samridh, Allyson, Rishi, Luka | 5/31/2022</p>
</div>
</body>
</html>
