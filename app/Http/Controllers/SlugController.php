<?php

namespace App\Http\Controllers;

use Request;
use App\NewsArticle, App\NewsCategory, App\NewsSubcategory, App\College;

class SlugController extends Controller
{
    /**
	 * reset or build the slugs for plexuss.
	 *
	 * @return string
	 */
	public function resetAllSlugs(){


		// reset the news categories slugs
		$newscategoryResults = NewsCategory::select('id', 'slug', 'name')->get();
		foreach ($newscategoryResults  as $key => $value) {
			$value->resluggify();
			$value->save();
		}
		echo "Done adding news catagories slugs<br/>";


		// reset the news sub categories slugs
		$newsSubcategoryResults = NewsSubcategory::select('id', 'slug', 'name')->get();
		foreach ($newsSubcategoryResults  as $key => $value) {
			$value->resluggify();
			$value->save();
		}
		echo "Done adding news sub catagories slugs<br/>";


		// reset the news articles slugs
		$newsResults = NewsArticle::select('id', 'slug', 'title' )->get();
		foreach ($newsResults as $key => $value) {
			$value->resluggify();
			$value->save();
		}
		echo "Done adding news articles slugs<br/>";


		// reset the college slugs
		$collegeResults = College::select('id', 'slug', 'school_name')->get();
		foreach ($collegeResults  as $key => $value) {
			$value->resluggify();
			$value->save();
		}
		echo "Done adding college slugs<br/>";

		return 'Done with slugs.';
	}
}
