<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\AgendaEventResource;
use App\Http\Resources\Public\EquipmentItemResource;
use App\Http\Resources\Public\PlaceResource;
use App\Http\Resources\Public\RentalPackResource;
use App\Http\Resources\Public\ServiceResource;
use App\Models\AgendaEvent;
use App\Models\EquipmentCategory;
use App\Models\EquipmentItem;
use App\Models\Place;
use App\Models\RentalPack;
use App\Models\Service;
use App\Support\Translation\Localized;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/** Lecture publique : studio, matériel à louer, packs, agenda et lieux. */
class ImpactLiveController extends Controller
{
    public function services(Request $request): AnonymousResourceCollection
    {
        return ServiceResource::collection(Service::published()->with(Localized::eager())
            ->when($request->query('activity'), fn (Builder $q, $activity) => $q->where('activity', $activity))
            ->orderBy('position')->orderBy('id')->get());
    }

    public function categories(): JsonResponse
    {
        $published = fn ($q) => $q->published()->where('usage', 'rental');

        return response()->json(['data' => EquipmentCategory::withCount(['items' => $published])->orderBy('position')->get()
            ->filter(fn (EquipmentCategory $c) => $c->items_count > 0)->values()
            ->map(fn (EquipmentCategory $c) => ['id' => $c->id, 'name' => $c->name, 'slug' => $c->slug, 'summary' => $c->summary, 'itemsCount' => $c->items_count])]);
    }

    public function equipment(Request $request): AnonymousResourceCollection
    {
        return EquipmentItemResource::collection(EquipmentItem::published()->with('category')
            ->where('usage', $request->query('usage', 'rental'))
            ->when($request->query('category'), fn (Builder $q, $slug) => $q->whereHas('category', fn ($c) => $c->where('slug', $slug)))
            ->when($request->boolean('featured'), fn (Builder $q) => $q->where('is_featured', true))
            ->orderBy('position')->orderBy('name')->get());
    }

    public function equipmentItem(string $slug): EquipmentItemResource
    {
        return (new EquipmentItemResource(EquipmentItem::published()->with('category')->where('slug', $slug)->firstOrFail()))->full();
    }

    public function packs(): AnonymousResourceCollection
    {
        return RentalPackResource::collection(RentalPack::published()->orderBy('position')->get());
    }

    public function agenda(Request $request): AnonymousResourceCollection
    {
        $query = AgendaEvent::published()->with(['place', ...Localized::eager()])
            ->when($request->query('activity'), fn (Builder $q, $activity) => $q->where('activity', $activity));

        $events = $request->query('scope') === 'references'
            ? $query->where('is_reference', true)->orderBy('position')->orderByDesc('starts_at')->get()
            : $query->upcoming()->orderBy('starts_at')->get();

        return AgendaEventResource::collection($events);
    }

    public function agendaEvent(string $slug): AgendaEventResource
    {
        return new AgendaEventResource(AgendaEvent::published()->with(['place', ...Localized::eager()])->where('slug', $slug)->firstOrFail());
    }

    public function places(Request $request): AnonymousResourceCollection
    {
        return PlaceResource::collection(Place::published()->with(Localized::eager())
            ->when($request->query('kind'), fn (Builder $q, $kind) => $q->where('kind', $kind))
            ->orderBy('position')->get());
    }
}
