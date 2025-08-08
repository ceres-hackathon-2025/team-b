<div class="sticky top-0 flex justify-between bg-white text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:text-gray-400 dark:border-gray-700">

        <a href="{{ route('home') }}" class="flex-shrink-0 ml-3">
            <img src="{{ asset('/666.png') }}"  class="h-11 w-auto cursor-pointer mt-1.5">
        </a>


        <ul class="flex flex-wrap -mb-px">
            <li class="me-2">
                <a href="#" class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300">おすすめ</a>
            </li>
            <li class="me-2">
                <a href="#" class="inline-block p-4 text-blue-600 border-b-2 border-blue-600 rounded-t-lg active dark:text-blue-500 dark:border-blue-500" aria-current="page">ランダム</a>
            </li>
        </ul>
        <a href="{{ route('search.view') }}" class="z-50 inset-y-0 end-0 flex items-center ps-3">
        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
        </svg>
    </a>
    <div class ="w-1"></div>
</div>
