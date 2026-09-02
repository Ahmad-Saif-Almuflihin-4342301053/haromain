<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-6">
    @for ($i = 0; $i < ($count ?? 6); $i++)
        <div class="bg-white rounded-xl shadow px-4 pb-4 border border-gray-300 flex flex-col animate-pulse">
            <div class="relative -mx-4 mt-0 mb-2">
                <div class="absolute inset-0 rounded-t-xl border-t border-x border-gray-300 pointer-events-none z-10">
                </div>
                <div class="rounded-t-xl w-full h-40! bg-gray-200"></div>
            </div>
            <div class="flex justify-start mt-2 mb-2">
                <span
                    class="inline-flex items-center gap-1 bg-gray-300 text-transparent text-xs font-semibold px-5 py-2 rounded-full w-24 h-6"></span>
            </div>
            <div class="flex-1 flex flex-col gap-2">
                <div class="h-5 bg-gray-200 rounded w-3/4 mb-2"></div>
                <div class="h-4 bg-gray-200 rounded w-1/2 mb-1"></div>
                <div class="h-4 bg-gray-200 rounded w-2/3 mb-1"></div>
                <div class="h-4 bg-gray-200 rounded w-1/3 mb-1"></div>
                <div class="h-4 bg-gray-200 rounded w-1/2 mb-1"></div>
                <div class="h-4 bg-gray-200 rounded w-1/4 mt-2"></div>
                <div class="h-6 bg-gray-200 rounded w-1/2 mb-1"></div>
                <div class="h-4 bg-amber-100 rounded w-1/3"></div>
            </div>
            <div class="mt-4 bg-amber-100 text-transparent text-center font-semibold py-2 rounded w-full h-10"></div>
        </div>
    @endfor
</div>
