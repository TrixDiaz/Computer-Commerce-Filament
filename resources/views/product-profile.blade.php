<x-noauth-layout>
  @if(isset($error))
    <div class="text-red-500">{{ $error }}</div>
  @else
    <livewire:product-profile :slug="$slug" />
  @endif
</x-noauth-layout>