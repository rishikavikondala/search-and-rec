html>
<head>
      	<title>UW Course Catalog Search</title>
</head>

<body>

<h1>UW Course Catalog Search</h1>
<p>Use this system to search for classes at the University of Washington! Enter a keyword to get started.</p>
<p>Ex. if you're interested in journalism, try searching for "journalism".</p>

<form action="search.php" method="post">
        <input type="text" size=40 name="search_string" value="<?php echo $_POST["search_string"];?>"/>
        <input type="submit" value="Search"/>
</form>

<?php
     	if (isset($_POST["search_string"])) {
                $search_string = $_POST["search_string"];
                $qfile = fopen("query.py", "w");

                // SEARCH QUERY LOGGING CODE STARTS HERE
                $query_file = fopen("queries.txt", "a") or die("Error opening file");
                fwrite($query_file, $search_string . "\n");
                fclose($query_file);
                // SEARCH QUERY LOGGING CODE ENDS HERE

                fwrite($qfile, "import pyterrier as pt\nif not pt.started():\n\tpt.init()\n\n");
                fwrite($qfile, "import pandas as pd\nqueries = pd.DataFrame([[\"q1\", \"$search_string\"]], columns=[\"qid\",\"query\"])\n");
                fwrite($qfile, "index = pt.IndexFactory.of(\"./uw_index/\")\n");
                fwrite($qfile, "tf_idf = pt.BatchRetrieve(index, wmodel=\"TF_IDF\")\n");

                for ($i=0; $i<5; $i++) {
                        fwrite($qfile, "print(index.getMetaIndex().getItem(\"filename\",tf_idf.transform(queries).docid[$i]))\n");
                        fwrite($qfile, "print(index.getMetaIndex().getItem(\"title\",tf_idf.transform(queries).docid[$i]))\n");
                }

                fclose($qfile);

                exec("ls | nc -u 127.0.0.1 10155");
                sleep(3);

                $stream = fopen("output", "r");

                $line=fgets($stream);

                while(($line=fgets($stream))!=false) {
                        $clean_line = preg_replace('/\s+/',',',$line);
                        $record = explode("./", $clean_line);
                        $line = fgets($stream);
                        echo "<a href=\"http://$record[1]\">".$line."</a><br/>\n";
                }

                fclose($stream);

                exec("rm query.py");
                exec("rm output");
                }
?>