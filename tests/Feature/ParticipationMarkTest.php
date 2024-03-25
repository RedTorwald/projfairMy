<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Project;
use App\Models\Supervisor;
use App\Models\Candidate;
use App\Models\Participation;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ParticipationMarkTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function supervisor_can_update_participation_mark()
    {
        
        $supervisorCreator = Supervisor::factory()->create([
            'api_token' => 'token_example'
        ]);
        
        $project = Project::factory()->create();
        
        $candidate = Candidate::factory()->create();

        
        $participation = Participation::factory()->create([
            'project_id' => $project->id,
            'candidate_id' => $candidate->id,
        ]);

      
        $updatedParticipation = $this->patch("api/supervisor/projects/{$project->id}/candidates/{$candidate->id}", [
            'mark' => 4,
        ], [
            "token" => $supervisorCreator->api_token
        ]);

        $updatedParticipation->assertStatus(200);
        
        $this->assertEquals(4, Participation::find($participation->id)->mark);
    }
}

