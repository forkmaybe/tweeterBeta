<?php

// Db connection setup
$db_connection = mysql_connect( 'localhost', 'root', 'scraping' );
$db = mysql_select_db( "live");

if (!$db_connection || !$db) {
    die("Problem with connection to the database.");
}
global $db_connection, $db;
prepTweet(0);

/*
  Function to retreive a given recruiters jobs titles
*/
function prepTweet($upToFourDays) {
    $star='<img src="star.png"/>';
    $brokenCompany='<img src="redx.png"/>';

    echo "<style>p {background-color: lightblue;}  .right{position:absolute;right:10px;top:0;}</style>";

    echo '<h1>Tweets to Tweet</h1>    <div class="right"> '.$star.' = featured    '.$brokenCompany.' = not active</div>';
    //adding all skills to the skills array from db
    $result = mysql_query("SELECT `skill` FROM `skills`");

    $skills = array();
    $z=0;
    while($row = mysql_fetch_assoc($result))
    {
        $skills[] = $row["skill"];
        $z++;

    }//now we have all skills

    $q=0;
    $now = date("Y/m/d H:i:s", $_SERVER['REQUEST_TIME']);//converts from unix time to formatted time for php

    $date1=date_create($now);

    $name = array();

    $dates = mysql_query("SELECT * FROM `jobs`");
    $nj = 0;
    $count=0;
    $numberJobsNeeded = 100;
    if (mysql_num_rows($dates) == 0) {
        echo "No jobs found.<Br/>";
    }
    else {
        while ($row = mysql_fetch_assoc($dates)) {
            $featured = $row["job_featured"];
            $job_id[] = $row["job_id"];
            $date_time_inserted[] = $row["re_adv"];//time inserted
            $insertedDateTime = $date_time_inserted[$q];
            $date2 = date_create($insertedDateTime);
            $diff = date_diff($date1, $date2);
            if ($diff->format("%R%a") == "-0" or $diff->format("%R%a") == "-1" or $diff->format("%R%a") == "-2" or $diff->format("%R%a") == $upToFourDays or $featured =="Yes") {
                $featured=$title=$url=$jobType=$location=$ht1=$ht2=$ht3=$companyTwitter=$companyName="";
                $featured = $row["job_featured"];
                $location=$row["job_location"];
                $url=$row["url"];
                $id=$row["recruiter_id"];
                $title=$row["job_title"];
                $saURL = "www.siliconarmada.com/$job_id[$q]/job.com";
                $allRecs = mysql_query("SELECT * FROM `recruiter` where `recruiter_id` = '$id'");

                while ($r = mysql_fetch_assoc($allRecs)) {
                    $companyName = $r["recruiter_company_name"];
                    $companyTwitter = $r["recruiter_twitter_username"];
                }//now we have all recs

                $jt = $row["job_type"];
                $jtWord = mysql_query("SELECT * FROM `job_type` where `id` = '$jt'");

                while ($jtRow = mysql_fetch_assoc($jtWord)) {
                    $jobType = $jtRow["type_name"];
                }//3rd hashtag

                if($row["key1"] != null and $row["key1"] != "") {
                    $skill = $row["key1"];
                    $skillHashTags = mysql_query("SELECT * FROM `skills` where `skill` = '$skill'");

                    while ($skillRow = mysql_fetch_assoc($skillHashTags)) {
                        $ht1 = $skillRow["twitterKeyword"];
                    }//1st hashtag
                    if ($row["key2"] != null and $row["key2"] != "") {
                        $skill = $row["key2"];
                        $skillHashTags = mysql_query("SELECT * FROM `skills` where `skill` = '$skill'");

                        while ($skillRow = mysql_fetch_assoc($skillHashTags)) {
                            $ht2 = $skillRow["twitterKeyword"];
                        }//2nd hashtag
                        if ($row["key3"] != null and $row["key3"] != "") {
                            $skill = $row["key3"];
                            $skillHashTags = mysql_query("SELECT * FROM `skills` where `skill` = '$skill'");

                            while ($skillRow = mysql_fetch_assoc($skillHashTags)) {
                                $ht3 = $skillRow["twitterKeyword"];
                            }//3rd hashtag
                        }
                    }
                }
                if($row["job_status"]=="No"){
                    $bc = $brokenCompany;
                }
                else{$bc="";}

                if($featured=="Yes"){
                    $s = $star;
                }
                else{$s="";}
                $tweet = "<p>".$s."".$bc."".$row['job_title']." ".$companyTwitter." ".$row['job_location']." ".$saURL." ".$ht1." ".$ht2." ".$ht3."</p>";

                echo "$tweet";

                $file = 'tweets.txt';
// Open the file to get existing content
                $current = file_get_contents($file);
// Append a new person to the file
                $current .= $tweet;
// Write the contents back to the file
                file_put_contents($file, $current);
                $count++;
                $new_job[] = $row["job_id"];
                //echo $count . "\t" . $new_job[$nj] . "\t" . $diff->format("%R%a") . "\t" . $jobType . "\t" . $location. "\t" . $companyName . "\t" . $companyTwitter . "\t". $row["level"] . "\t"  . $ht1 . "\t" . $ht2 . "\t" . $ht3. "\n";
                $nj++;
                //addPotentialTweet($new_job[$nj]);


            }

            $q++;
        }
        if ($nj < $numberJobsNeeded) {
            echo $nj." jobs found";
            echo " loading up to 4 day old jobs for tweets...";
            prepTweet(-3);//up to four days
        }

    }


    function addPotentialTweet($job_id){





        $array = array(
            "job_id" => "' '",
            "job_source" => "jobsite",

        );
    }


    class  Tweet{
        /* Member variables */
        var $timeZone;
        var $timeToTweet;
        var $amountCharacters;
        var $country;
        var $featured;
        var $title;

        /* Member functions */
        function setTimeZone($par){
            $this->timeZone = $par;
        }

        function getTimeZone(){
            echo $this->timeZone;
        }

        function setTitle($par){
            $this->title = $par;
        }

        function getTitle(){
            echo $this->title;
        }
    }


    function timeDif() {
        $now = date("Y/m/d H:i:s", $_SERVER['REQUEST_TIME']);

    }

}




?>
