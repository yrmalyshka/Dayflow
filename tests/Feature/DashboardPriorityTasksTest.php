<?php

namespace Tests\Feature;

use Tests\TestCase;

class DashboardPriorityTasksTest extends TestCase
{
    public function test_dashboard_renders_high_priority_tasks_from_page_controller_data(): void
    {
        $response = $this->withSession([
            'tasks' => [[
                'id' => 99,
                'title' => 'Stale session task',
                'category' => 'Work',
                'priority' => 'Low',
                'status' => 'To Do',
                'date' => '2026-11-06',
                'time' => '09:00',
            ]],
        ])->get('/dashboard?username=Tester');

        $response->assertOk();
        $response->assertSee('Prepare Presentation');
        $response->assertSee('High');
        $response->assertDontSee('Stale session task');
        $response->assertDontSee('Team Project Meeting');
    }
}
