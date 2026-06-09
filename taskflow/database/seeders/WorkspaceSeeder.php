<?php

namespace Database\Seeders;

use App\Containers\AppSection\Authorization\Models\Role;
use App\Containers\AppSection\User\Models\User;
use App\Containers\ClientSection\WorkSpace\Models\Workspace;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WorkspaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $workspaces = [
            [
                "name" => "Goriovn",
                "description" => "Công ty TNHH Goriovietnam",
            ],
            [
                "name" => "Viettel",
                "description" => "Tập đoàn Viễn thông Quân đội",
            ],
            [
                "name" => "VinGroup",
                "description" => "Tập đoàn Vingroup",
            ],
        ];
        foreach ($workspaces as $workspace) {
            if($workspace['name'] == 'Viettel'){
                $ownerId = 4;
            }elseif($workspace['name'] == 'VinGroup'){
                $ownerId = 5;
            }else{
                $ownerId = 3;
            }
            $workspaceModel = Workspace::create([
                "name" => $workspace['name'],
                "description" => $workspace['description'],
                "owner_id" => $ownerId,
                "slug" => Str::slug($workspace['name']),
            ]);

            setPermissionsTeamId($workspaceModel->id);
            $ownerModel = User::find($ownerId);
            $ownerModel->assignRole(Role::ROLE_OWNER);
        }
    }
}
