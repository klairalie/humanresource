@props(['title'])

<div @class(['card', $title])>
    <div class="flex justify-between items-center bg-gray-400">
        <div>
            <h1>
                {{ $slot }}
            </h1>
        </div>
       
        <div>
             <!--<a {{ $attributes }} class="btn-view-details"> View Details</a> -->
        </div>
    </div>
</div>
