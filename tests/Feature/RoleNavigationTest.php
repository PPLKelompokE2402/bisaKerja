<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class RoleNavigationTest extends TestCase
{
    public function test_login_page_has_the_role_specific_post_login_destinations(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('admin: "'.route('admin.dashboard').'"', false)
            ->assertSee('pencari_kerja: "'.route('jobs.index').'"', false)
            ->assertSee('penyedia_pekerjaan: "'.route('employer.vacancies.index').'"', false);
    }

    public function test_guest_is_sent_to_login_and_authenticated_roles_reach_their_own_home(): void
    {
        $this->get(route('home'))->assertRedirectToRoute('login');

        $this->actingAs($this->userWithRole('pencari_kerja'))
            ->get(route('home'))
            ->assertRedirectToRoute('jobs.index');

        $this->actingAs($this->userWithRole('penyedia_pekerjaan'))
            ->get(route('home'))
            ->assertOk()
            ->assertViewIs('jobs.home')
            ->assertSee('data-provider-navigation', false)
            ->assertSee(route('home', ['view' => 'pelamar-saya']), false);

        $this->actingAs($this->userWithRole('admin'))
            ->get(route('home'))
            ->assertRedirectToRoute('admin.dashboard');
    }

    public function test_seeker_application_entry_is_limited_to_job_seekers(): void
    {
        $this->get(route('seeker.applications'))->assertRedirectToRoute('login');

        $this->actingAs($this->userWithRole('pencari_kerja'))
            ->get(route('seeker.applications'))
            ->assertOk()
            ->assertViewIs('seeker.applications-entry')
            ->assertSee(route('home'), false);

        foreach (['penyedia_pekerjaan', 'admin'] as $role) {
            $this->actingAs($this->userWithRole($role))
                ->get(route('seeker.applications'))
                ->assertForbidden();
        }
    }

    public function test_landing_navigation_does_not_link_to_another_roles_pages(): void
    {
        $seekerView = $this->actingAs($this->userWithRole('pencari_kerja'))
            ->view('jobs.welcome', ['jobs' => collect(), 'categories' => collect()]);
        $seekerView->assertSee(route('seeker.applications'))
            ->assertDontSee(route('employer.vacancies.index'))
            ->assertDontSee(route('admin.dashboard'));

        $providerView = $this->actingAs($this->userWithRole('penyedia_pekerjaan'))
            ->view('jobs.welcome', ['jobs' => collect(), 'categories' => collect()]);
        $providerView->assertSee(route('employer.vacancies.index'))
            ->assertSee(route('home', ['view' => 'pelamar-saya']))
            ->assertDontSee(route('seeker.applications'))
            ->assertDontSee(route('admin.dashboard'));

        $adminView = $this->actingAs($this->userWithRole('admin'))
            ->view('jobs.welcome', ['jobs' => collect(), 'categories' => collect()]);
        $adminView->assertSee(route('admin.dashboard'))
            ->assertSee(route('admin.verifikasi'))
            ->assertSee(route('admin.pengguna'))
            ->assertDontSee(route('seeker.applications'))
            ->assertDontSee(route('employer.vacancies.index'));
    }

    public function test_web_logout_clears_the_authenticated_session(): void
    {
        $this->actingAs($this->userWithRole('penyedia_pekerjaan'))
            ->post(route('logout'))
            ->assertRedirectToRoute('login');

        $this->assertGuest();
        $this->get(route('home'))->assertRedirectToRoute('login');
    }

    private function userWithRole(string $role): User
    {
        return (new User)->forceFill([
            'id_pengguna' => 1,
            'role' => $role,
            'status_akun' => 'aktif',
            'nama' => 'Pengguna Tes',
        ]);
    }
}
