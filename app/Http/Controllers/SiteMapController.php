<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\College;

class SiteMapController extends Controller
{

	protected $variation_num = 1;
	public function run(){

		$colleges = College::where('id', '>=', '6001')
			->where('id', '<=', '7769')->get();

		foreach ($colleges as $key) {
			echo "  
  <url>
    <loc>https://plexuss.com/college/".$key->slug."</loc>
    <lastmod>2014-12-19</lastmod>
    <changefreq>Weekly</changefreq>
  </url>";
  echo "
  <url>
    <loc>https://plexuss.com/college/".$key->slug."/overview</loc>
    <lastmod>2014-12-19</lastmod>
    <changefreq>Weekly</changefreq>
  </url>";
    echo "
  <url>
    <loc>https://plexuss.com/college/".$key->slug."/stats</loc>
    <lastmod>2014-12-19</lastmod>
    <changefreq>Weekly</changefreq>
  </url>";
      echo "
  <url>
    <loc>https://plexuss.com/college/".$key->slug."/ranking</loc>
    <lastmod>2014-12-19</lastmod>
    <changefreq>Weekly</changefreq>
  </url>";
      echo "
   <url>
    <loc>https://plexuss.com/college/".$key->slug."/admissions</loc>
    <lastmod>2014-12-19</lastmod>
    <changefreq>Weekly</changefreq>
  </url>";
      echo "
  <url>
    <loc>https://plexuss.com/college/".$key->slug."/financial-aid</loc>
    <lastmod>2014-12-19</lastmod>
    <changefreq>Weekly</changefreq>
  </url>";
      echo "
  <url>
    <loc>https://plexuss.com/college/".$key->slug."/enrollment</loc>
    <lastmod>2014-12-19</lastmod>
    <changefreq>Weekly</changefreq>
  </url>";
      echo "
  <url>
    <loc>https://plexuss.com/college/".$key->slug."/tuition</loc>
    <lastmod>2014-12-19</lastmod>
    <changefreq>Weekly</changefreq>
  </url>";
		}
	}
}
