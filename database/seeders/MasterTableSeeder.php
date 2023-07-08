<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\ChapterState;
use App\Models\MetroAreas;
use Illuminate\Database\Seeder;
use App\Models\MembershipCategory;

class MasterTableSeeder extends Seeder
{

    public function run(): void
    {
        //membership..
        $membership_data = [
            [1, "Community Member", "Community Member with free membership", "0"],
            [2, "Donor Member", "Donor membership with $50 application fee", "50"],
            [3, "Power Donor Member", "Power Donor membership with $500 application fee", "500"],
            [4, "Mega Donor Member", "Mega Donor membership with $5000 application fee", "5000"]
        ];

        foreach ($membership_data as $member_ship) {
            MembershipCategory::updateOrCreate([
                'id' => $member_ship[0],
                'name' => $member_ship[1],
                'description' => $member_ship[2],
                'fee' => $member_ship[3],
            ]);
        }

        //chapters..
        $chapters = [
            [1, "New England", "New England Chapter"],
            [2, "Empire", "Empire Chapter"],
            [3, "Capitol", "Capitol Chapter"],
            [4, "South East", "South East Chapter"],
            [5, "North Texas", "North Texas Chapter"],
            [6, "South Texas", "South Texas Chapter"],
            [7, "Central", "Central Chapter"],
            [8, "North West", "North West Chapter"],
            [9, "South West", "South West Chapter"],
        ];

        foreach ($chapters as $chapter) {
            Chapter::updateOrCreate([
                'id' => $chapter[0],
                'name' => $chapter[1],
                'description' => $chapter[2],
            ]);
        }

        //states..
        $states = [
            [1, "ME", "Maine"],
            [1, "VT", "Vermont"],
            [1, "NH", "New Hampshire"],
            [1, "MA", "Massachusets"],
            [1, "RI", "Rhode Island"],
            [1, "CT", "Connecticut"],
            [2, "NY", "New York"],
            [2, "NJ", "New Jersey"],
            [2, "PA", "Pennsylvania"],
            [3, "DE", "Delaware"],
            [3, "DC", "District of Columbia"],
            [3, "MD", "Maryland"],
            [3, "VA", "Varginia"],
            [3, "WV", "West Virginia"],
            [4, "GA", "Georgia"],
            [4, "SC", "South Carolina"],
            [4, "NC", "North Carolina"],
            [4, "FL", "Florida"],
            [4, "TN", "Tennessee"],
            [4, "KY", "Kentucky"],
            [5, "TX", "Texas", "North/DFW"],
            [5, "OK", "Oklahama"],
            [5, "AR", "Arkansas"],
            [6, "TX", "Texas", "South/Houston/Austin/San Auntonio"],
            [6, "LA", "Louisiana"],
            [7, "ND", "North Dakota"],
            [7, "SD", "South Dakota"],
            [7, "NE", "Nebraska"],
            [7, "KS", "Kansas"],
            [7, "MN", "Minnesota"],
            [7, "IA", "Iowa"],
            [7, "MO", "Montana"],
            [7, "WI", "Wisconsin"],
            [7, "IL", "Illinois"],
            [7, "MI", "Michigan"],
            [7, "IA", "Indiana"],
            [7, "OH", "Ohio"],
            [8, "AK", "Alaska"],
            [8, "WA", "Washington"],
            [8, "OR", "Oregon"],
            [8, "CA", "California", "Bay Area"],
            [8, "MO", "Montana"],
            [8, "WY", "Wyoming"],
            [8, "ID", "Idaho"],
            [9, "CA", "California", "SoCal"],
            [9, "NV", "Nevada"],
            [9, "AZ", "Arizona"],
            [9, "NM", "New Mexico"],
            [9, "UT", "Utah"],
            [9, "CO", "Colarado"],
        ];

        foreach ($states as $state) {
            $stateRow = ChapterState::updateOrCreate([
                'chapter_id' => $state[0],
                'name' => $state[2],
                'short_name' => $state[1],
            ]);

            $areaNames = array_key_exists(3,$state) ? [$state[3]] : [];

            foreach ($areaNames as $areaName) {
                MetroAreas::updateOrCreate([
                    "name" => $areaName,
                    "chapter_state_id" => $stateRow["id"]
                ]);
            }

        }

    }
}
