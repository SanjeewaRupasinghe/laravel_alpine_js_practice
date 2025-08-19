@extends('layouts.app')

@section('content')
    <div x-data="productManager()" x-init="init()">

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
                    @foreach ($products as $product)
                        <tr>
                            <td class="p-2 border border-gray-300">{{ $product->id }}</td>
                            <td class="p-2 border border-gray-300">{{ $product->name }}</td>
                            <td class="p-2 border border-gray-300">{{ $product->price }}</td>
                            <td class="p-2 border border-gray-300">{{ $product->status }}</td>
                            <td class="p-2 border border-gray-300">
                                <img src="{{ asset('storage/' . $product->images->first()->image) }}" alt=""
                                    class="w-20 h-20">
                            </td>
                            <td class="p-2 border border-gray-300 flex gap-2">

                                <!-- view -->
                                <button title="View" @click="openModal('view', {{ $product }})"
                                    class="flex items-center cursor-pointer bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-white font-medium">
                                    <i data-lucide="eye" class="w-5 h-5 mr-1"></i>
                                </button>
                                <!-- END view -->

                                <!-- edit -->
                                <button title="Edit" @click="openModal('edit', {{ $product }})"
                                    class="flex items-center cursor-pointer bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-white font-medium">
                                    <i data-lucide="pencil" class="w-5 h-5 mr-1"></i>
                                </button>
                                <!-- END edit -->

                                <!-- delete -->
                                <button title="Delete" @click="openModal('delete', {{ $product }})"
                                    class="flex items-center cursor-pointer bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-white font-medium">
                                    <i data-lucide="trash" class="w-5 h-5 mr-1"></i>
                                </button>
                                <!-- END delete -->
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- END Table -->

        <!-- Include Modal -->
        @include('products.partials.product-modal')
        <!-- END Include Modal -->


        <!-- if any errors -->
        @if ($errors->any())
            <script>
                document.addEventListener('alpine:init', () - > {
                    Alpine.store('productStore', {
                        isModalOpen: true,
                    });
                }).catch(error => {
                    console.error(error);
                });
            </script>
        @endif
        <!-- END if any errors -->
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
                isView: false,

                // init lifecycle
                init() {
                    if (Alpine.store('productStore')?.isModalOpen) {
                        this.openModal('create');
                        Alpine.store('productStore').isModalOpen = false;
                    }
                },

                // open modal
                openModal(type, product = null) {
                    this.mode = type;
                    this.isView = type === 'view';
                    this.isModalOpen = true;
                    this.modalTitle = this.isView ? 'View Product' : type === 'create' ? 'Add Product' : 'Edit Product';
                    this.errors = [];
                    this.form = productManager.defaultForm();  

                    if (product) {
                        Object.assign(this.form, {
                            id:product.id,
                            name:product.name,
                            price:product.price,
                            status:product.status,
                            description:product.description,
                            existingImages:product.images.map(image=>image.image),
                        });

                        this.imagePreviews=product.images.map(img=>({                            
                            url:`/storage/${img.image}`,
                            type:'existing',
                            file:img.image,
                        }));
                        
                    }else{
                        this.imagePreviews=[];
                    }

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
                    this.$refs.images.files = dataTransfer.files;
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


                removeImage(index) {
                    const image = this.imagePreviews[index];

                    if (image.type === 'existing') {
                        this.form.existingImages = $this.form.existingImages.filter(path => path !== image.image);
                    } else if (image.type === 'new') {
                        const fileIndex = this.form.images.findIndex(file => URL.createObjectURL(file) === image.url);

                        if (fileIndex !== -1) {
                            this.form.images.splice(fileIndex, 1);
                        }
                    }

                    this.imagePreviews.splice(index, 1);
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
