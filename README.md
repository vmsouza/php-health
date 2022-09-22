# php-health

Class used for health analysis results. It returns BMI, BMR, TDEE, Polock 7 sinfolds stats (body fat, body density, lean mass, fat mass)

This class returns in object format:

* BMI
* Harris Benedict BMR TDEE
* Katch McArdle BMR and TDEE (with Jackson & Pollock 7 skinfold protocol results)
* Jackson and Pollock 7 Skinsfold Protocol (sum of skinfolds, body density, body fat,
* fat mass and lean mass)

It is not necessary to fill in all the fields because the class will only return data pertaining to the measurements provided.

```
<?php

require_once("health.class.php");
$h=new Health();

$h->age=46;
$h->gender="male";
$h->height=168;
$h->weight=80;

$h->activity=1.2;

$h->triceps=6;
$h->chest=9;
$h->subscapular=13;
$h->midaxillary=9;
$h->suprailiac=8;
$h->abdominal=15;
$h->thigh=11;

$data=$h->getdata();
print_r($data);

?>
```
Results:

```
stdClass Object
(
    [measurements] => stdClass Object
        (
            [height] => 168
            [weight] => 80
            [age] => 46
            [gender] => male
            [bmi] => stdClass Object
                (
                    [value] => 28.3
                    [category] => Overheight
                )

            [skinfolds] => stdClass Object
                (
                    [triceps] => 6
                    [chest] => 9
                    [subscapular] => 13
                    [midaxillary] => 9
                    [suprailiac] => 8
                    [abdominal] => 15
                    [thigh] => 11
                )

            [prot_7_skinfolds] => stdClass Object
                (
                    [skinfoldsum] => 71
                    [body_density] => 1.0706283
                    [bf] => 12.35
                    [fatmass] => 9.876
                    [leanmass] => 70.124
                )

            [harris_benedict] => stdClass Object
                (
                    [bmr] => 1705
                    [tdee] => 2046
                )

            [katch_mcardle] => stdClass Object
                (
                    [bmr] => 1885
                    [tdee] => 2262
                )
            
            [mifflin_st_jeor] => stdClass Object
                (
                    [bmr] => 1625
                    [tdee] => 1950
                )

        )

)
```
