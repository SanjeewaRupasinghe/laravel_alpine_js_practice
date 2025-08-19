<div x-show="isModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

    <div class="bg-white p-6 rounded-lg shadow-lg w-[500px]">
        <h2 class="text-lg font-semibold mb-4">Add Product</h2>
        <form :action="mode==='edit' ? `/products/${form.id}` : '/products'" method="POST" enctype="multipart/form-data">
            @csrf
            <template x-if="mode==='edit'">
                <input type="hidden" name="_method" value="PUT">
            </template>
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" id="name" name="name" class="mt-1 p-2 border rounded w-full">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4 grid grid-cols-2 gap-4">
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                    <input type="number" id="price" name="price" class="mt-1 p-2 border rounded w-full">
                    @error('price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select id="status" name="status" class="mt-1 p-2 border rounded w-full">
                        <option selected disabled value="">Select Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea id="description" name="description" class="mt-1 p-2 border rounded w-full"></textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="image" class="block text-sm font-medium text-gray-700">Images</label>

                <div @click="$refs.images.click()" @dragover.prevent @drop.prevent="handleDrop($event)" class="w-full border-2 border-dashed border-gray-300 px-4 py-20 cursor-pointer rounded text-center">
                    <input @change="handleImage($event)" x-ref="images" type="file" class="hidden" id="images" name="images[]" multiple accept="image/*">

                    <p class="text-gray-500 flex items-center">
                        <i data-lucide="upload" class="mr-2"></i> Drag and drop files here or click to upload
                    </p>
                    <p class="text-xs text-gray-500"> You can upload multiple images</p>
                </div>

            </div>

            @error('images')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror

            <!-- client side validation -->
            <div class="text-red-600 text-sm space-y-1">
                <template x-for="(error,index) in errors" :key="index">
                    <p x-text="error" class="text-red-500"></p>
                </template>
            </div>
            <!-- END client side validation -->

            <!-- images preview -->
            <div class="mb-4 grid grid-cols-2 sm:grid-cols-3 gap-4">
                <template x-for="(img,index) in imagePreviews" :key="index">
                    <div class="relative group w-full h-36 rounded overflow-hidden shadow-md border border-gray-200">
                        <img :src="img.url" class="w-full h-24 object-cover">
                        <button @click="removeImage(index)" class="absolute top-2 right-2 bg-red-500 text-white p-1 text-xs rounded">
                            x
                        </button>
                    </div>
                </template>
            </div>
            <!-- END images preview -->

            <div class="flex justify-end w-full">
                <button @click="closeModal" type="button" class="bg-gray-200 text-gray-800 px-4 py-2 rounded mr-2 hover:bg-gray-300">
                    <div class="flex items-center">
                        <i data-lucide="x" class="mr-1"></i> Cancel
                    </div>
                </button>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    <div class="flex items-center">
                        <i data-lucide="save" class="mr-1"></i> Add Product
                    </div>
                </button>

            </div>

        </form>
    </div>



</div>
