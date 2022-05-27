<html>
<head>
      	<title>UW Course Catalog Search</title>
<style>
      * {box-sizing: border-box; font-family: "Arial";}
      .column {float: left;}
      .search {width: 70%;}
      .rec {width: 30%;}
      .interactive:after {content: ""; display: table; clear: both;}
      .footer {position: fixed; left:0; bottom: 0; width: 100%; background-color: #4B2E83; color: #D7DBDD; text-align: center;}
</style>
</head>

<body style="padding: 25px 100px 25px 100px">

<h1>UW Course Catalog Search</h1>
<p>Use this system to search for classes at the University of Washington! Enter a keyword to get started.</p>
<p>Ex. if you're interested in journalism, try searching for "journalism".</p>

<!-- LOADING ICON LIBRARY FOR SEARCH ICON -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<!-- DIVIDING SEARCH & REC INTO DIFFERENT COLUMNS ON PAGE -->
<div class="interactive">
      <!-- START OF SEARCH -->
      <div class="column search">
            <form action="search.php" method="post">
                  <input type="text" size=40 name="search_string" placeholder="Search..." value="<?php echo $_POST["search_string"];?>"/>
                  <button id="request" type="submit"><i class="fa fa-search"></i></button>
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
                            fwrite($qfile, "index = pt.IndexFactory.of(\"./reddit_index/\")\n");
                            fwrite($qfile, "tf_idf = pt.BatchRetrieve(index, wmodel=\"TF_IDF\")\n");

                            for ($i=0; $i<5; $i++) {
                                    fwrite($qfile, "print(index.getMetaIndex().getItem(\"filename\",tf_idf.transform(queries).docid[$i])[:-10])\n");
                                    fwrite($qfile, "print(index.getMetaIndex().getItem(\"title\",tf_idf.transform(queries).docid[$i]))\n");
                            }

                            fclose($qfile);

                            exec("ls | nc -u 127.0.0.1 10155");
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
      
      <!-- START OF REC -->
      <div class="column rec">
            <p>Recent Searches</p>
            
            <!-- REPLACE WITH RECOMMENDER RESULTS -->
            <ul>
                  <li onclick="addSearch(`Search 1`)">Search 1</li>
                  <li onclick="addSearch(`Search 2`)">Search 2</li>
                  <li onclick="addSearch(`Search 3`)">Search 3</li>
            </ul>
            
            <!-- JAVASCRIPT FOR AUTO SEARCHING SELECTED RECENT SEARCH -->
            <script>
                  <!-- TAKES IN STRING FORMAT OF QUERY -->
                  function addSearch(popQuery) {
                        document.getElementByName("search_string")[0].value = popQuery;
                        document.getElementById("request").form.submit();
                  }
            </script>
      </div>
</div>
      
<div class="footer">
      <p>Created by Samridh Bhattacharjee, Allyson Graylin, Rishi Kavikondala, Luka Marceta | 5/31/2022</p>
</div>
</body>
</html>
