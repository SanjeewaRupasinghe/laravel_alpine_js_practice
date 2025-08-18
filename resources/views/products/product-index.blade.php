@extends('layouts.app')

@section('content')
    <div x-data="productManager()">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Products</h1>
            <button @click="openModal('create')"
                class="flex items-center cursor-pointer bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-white font-medium">
                <i data-lucide="plus-circle" class="w-5 h-5 mr-1"></i> Add Product</button>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse border border-gray-500">
                <thead>
                    <tr>
                        <th class="p-2 border border-gray-300">#</th>
                        <th class="p-2 border border-gray-300">Name</th>
                        <th class="p-2 border border-gray-300">Price</th>
                        <th class="p-2 border border-gray-300">Status</th>
                        <th class="p-2 border border-gray-300">Image</th>
                        <th class="p-2 border border-gray-300">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- @foreach ($products as $product)
                    <tr>
                        <td class="p-2 border border-gray-300">{{ $product->id }}</td>
                        <td class="p-2 border border-gray-300">{{ $product->name }}</td>
                        <td class="p-2 border border-gray-300">{{ $product->price }}</td>
                        <td class="p-2 border border-gray-300">
                            <a href="{{ route('products.edit', $product->id) }}" class="mr-2">Edit</a>
                            <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach --}}
                </tbody>
            </table>
        </div>
        <!-- END Table -->

        <!-- Include Modal -->
        @include('products.partials.product-modal')
        <!-- END Include Modal -->
    </div>
@endsection

@push('scripts')
    <script>
        function productManager() {
            return {

                // Alpine states
                isModalOpen: false,
                mode: 'create',
                modalTitle: 'Add Product',
                form: productManager.defaultForm(),
                imagePreviews: [],
                errors: [],

                // open modal
                openModal(type) {
                    this.isModalOpen = true;
                },

                // close modal
                closeModal() {
                    this.isModalOpen = false;
                },

                // handle image
                handleImage(event) {
                    const files = Array.from(event.target.files);
                    this.processFileHandling(files);
                },

                // handle drop
                handleDrop(event) {
                    const files = Array.from(event.dataTransfer.files);
                    this.processFileHandling(files);

                    // Attaching dropped files to the actual file input
                    const dataTransfer = event.dataTransfer;
                    files.forEach(file => dataTransfer.items.add(file));
                    this.$refs.images.files=dataTransfer.files;
                },

                // process file handling
                processFileHandling(files) {

                    files.forEach(file => {
                        if (file.type.startsWith('image/')) {
                            this.form.images.push(file);
                            this.imagePreviews.push({
                                url: URL.createObjectURL(file),
                                type: file.type,
                                file
                            });
                        } else {
                            this.errors.push(`${file.name} is not valid image file`);
                        }
                    });

                },


            }
        }

        // return reusable function
        productManager.defaultForm = function() {
            return {
                name: '',
                price: '',
                status: '',
                description: '',
                images: [],
                existingImages: [],
            }
        }
    </script>
@endpush
