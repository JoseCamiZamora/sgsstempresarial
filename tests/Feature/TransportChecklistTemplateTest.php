<?php

namespace Tests\Feature;

use App\Models\{TransportChecklistTemplate, User};
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TransportChecklistTemplateTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        $user = User::role(['Super Admin', 'Administrador SGSST'])->whereNotNull('company_id')->first();
        if (!$user) {
            $this->markTestSkipped('Sin usuario administrador para la prueba.');
        }

        return $user;
    }

    public function test_store_accepts_more_than_two_items(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->post(route('transport.settings.checklists.store'), [
            'name' => 'Preoperacional QA',
            'blocks_on_critical_failure' => '1',
            'items' => [
                ['label' => 'Frenos', 'is_critical' => '1'],
                ['label' => 'Luces'],
                ['label' => 'Botiquín'],
                ['label' => 'Extintor', 'is_critical' => '1'],
            ],
        ]);

        $response->assertRedirect();
        $template = TransportChecklistTemplate::where('company_id', $admin->company_id)->where('name', 'Preoperacional QA')->first();
        $this->assertNotNull($template);
        $this->assertCount(4, $template->items);
        $this->assertSame(2, $template->items->where('is_critical', true)->count());
    }

    public function test_update_edits_existing_items_and_appends_new_ones_without_deleting(): void
    {
        $admin = $this->admin();
        $template = TransportChecklistTemplate::create(['company_id' => $admin->company_id, 'name' => 'Original', 'blocks_on_critical_failure' => false, 'status' => 'active']);
        $item1 = $template->items()->create(['label' => 'Item viejo', 'sort_order' => 1, 'is_critical' => false, 'is_required' => true]);

        $response = $this->actingAs($admin)->put(route('transport.settings.checklists.update', $template), [
            'name' => 'Renombrada',
            'blocks_on_critical_failure' => '1',
            'items' => [
                ['id' => $item1->id, 'label' => 'Item editado', 'is_critical' => '1'],
                ['label' => 'Item nuevo'],
            ],
        ]);

        $response->assertRedirect();
        $template->refresh();
        $this->assertSame('Renombrada', $template->name);
        $this->assertTrue((bool) $template->blocks_on_critical_failure);
        $this->assertCount(2, $template->items);
        $this->assertSame('Item editado', $item1->fresh()->label);
        $this->assertTrue((bool) $item1->fresh()->is_critical);
    }

    public function test_deactivate_and_activate_toggle_status(): void
    {
        $admin = $this->admin();
        $template = TransportChecklistTemplate::create(['company_id' => $admin->company_id, 'name' => 'Toggle QA', 'blocks_on_critical_failure' => false, 'status' => 'active']);

        $this->actingAs($admin)->delete(route('transport.settings.checklists.deactivate', $template))->assertRedirect();
        $this->assertSame('inactive', $template->fresh()->status);

        $this->actingAs($admin)->put(route('transport.settings.checklists.activate', $template))->assertRedirect();
        $this->assertSame('active', $template->fresh()->status);
    }

    public function test_cannot_update_or_toggle_another_companys_template(): void
    {
        $admin = $this->admin();
        $otherCompanyId = \App\Models\PerfilEmpresa::where('id', '!=', $admin->company_id)->value('id');
        if (!$otherCompanyId) {
            $this->markTestSkipped('Se necesita una segunda empresa para esta prueba.');
        }
        $template = TransportChecklistTemplate::create(['company_id' => $otherCompanyId, 'name' => 'Ajena', 'blocks_on_critical_failure' => false, 'status' => 'active']);

        $this->actingAs($admin)->put(route('transport.settings.checklists.update', $template), [
            'name' => 'Hackeada',
            'items' => [['label' => 'x']],
        ])->assertNotFound();
    }
}
