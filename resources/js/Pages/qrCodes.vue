<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import BreezeValidationErrors from '@/Components/ValidationErrors.vue';
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { Head, useForm, Link } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';

const displayModalQrCode = ref(false);

const props = defineProps({
    data: Object,
})

const qrCodes = ref(null);

onMounted(() => {
        qrCodes.value = props.data.qrCodes;
    }
);

const uploadForm = useForm({
    file_pdf: '',
    status: 'Submitted',
});

const uploadFile = () => {
    uploadForm.post(route('qrCodes.store'), {
        onSuccess: (data) => {
            qrCodes.value = props.data.qrCodes;
            displayModalQrCode.value = false;
        },
    });
};

const deleteQrCode = (qrCodeId) => {
     if(confirm("Do you really want to delete?")){
        Inertia.delete(route('qrCodes.destroy', [qrCodeId]), {
            onSuccess: (data) => {
                qrCodes.value = props.data.qrCodes;
                displayModalQrCode.value = false;
            },
        });
     }
};

</script>

<template>
    <Head title="QR Codes" />

    <BreezeAuthenticatedLayout>
        <template #header>
            <div class="grid grid-cols-4 gap-4">
                <div class="col-span-3">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        QR Codes
                    </h2>
                </div>
                <div class="">
                    <div class="flex space-x-2 justify-end">
                        <div>
                            <button type="button" class="inline-block px-6 py-2 border-2 border-blue-600 text-blue-600 font-medium text-xs leading-tight
                                uppercase rounded hover:bg-black hover:bg-opacity-5 focus:outline-none focus:ring-0 transition duration-150 ease-in-out"
                                @click="displayModalQrCode = true">
                                Upload QR Code
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- TABLE QR CODES -->

                        <div class="flex flex-col">
                            <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
                                <div class="py-2 inline-block min-w-full sm:px-6 lg:px-8">
                                    <div class="overflow-hidden">
                                        <table class="min-w-full">
                                            <thead class="bg-white border-b">
                                                <tr>
                                                    <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                                                        #
                                                    </th>
                                                    <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                                                        Name
                                                    </th>
                                                    <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                                                        Content
                                                    </th>
                                                    <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                                                        Status
                                                    </th>
                                                    <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                                                        Timestamp
                                                    </th>
                                                    <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                                                        Actions
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!--   The system should store the file name, timestamp, status (Submitted, onProcessing, Processed) and the content of the qr code. -->
                                                <tr v-for="(qrCode, index) in qrCodes" class="bg-white border-b transition duration-300 ease-in-out hover:bg-gray-100">
                                                    <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                        {{index+1}}
                                                    </td>
                                                    <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                        {{qrCode.name}}
                                                    </td>
                                                    <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                        {{qrCode.content}}
                                                    </td>
                                                    <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                        {{qrCode.status}}
                                                    </td>
                                                    <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                        {{qrCode.created_at}}
                                                    </td>
                                                    <td>
                                                        <a type="button" class="px-2
                                                            py-2.5
                                                            bg-blue-600
                                                            text-white
                                                            font-medium
                                                            text-xs
                                                            leading-tight
                                                            uppercase
                                                            rounded
                                                            shadow-md
                                                            hover:bg-blue-700 hover:shadow-lg
                                                            focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0
                                                            active:bg-blue-800 active:shadow-lg
                                                            transition
                                                            duration-150
                                                            ease-in-out
                                                            ml-1" :href="route('files', ['main', qrCode.name])"  target="_blank">download
                                                        </a>
                                                        <button type="button" class="px-2
                                                            py-2.5
                                                            bg-red-600
                                                            text-white
                                                            font-medium
                                                            text-xs
                                                            leading-tight
                                                            uppercase
                                                            rounded
                                                            shadow-md
                                                            hover:bg-red-700 hover:shadow-lg
                                                            focus:bg-red-700 focus:shadow-lg focus:outline-none focus:ring-0
                                                            active:bg-red-800 active:shadow-lg
                                                            transition
                                                            duration-150
                                                            ease-in-out
                                                            ml-1" @click="deleteQrCode(qrCode.id)" target="_blank">Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- END TABLE -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="transition-opacity duration-1000 bg-gray-500 z-50 bg-opacity-20 fixed top-0 left-0 w-full h-full outline-none overflow-x-hidden overflow-y-auto mx-auto"
            id="uploadQRCodeModal" v-show="displayModalQrCode" >
            <div class="max-w-lg mx-auto mt-7 relative w-auto pointer-events-none">
                <div
                class="border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div
                        class="modal-header flex flex-shrink-0 items-center justify-between p-4 border-b border-gray-200 rounded-t-md">
                        <h5 class="text-xl font-medium leading-normal text-gray-800" id="uploadQRCodeModalLabel">Upload QR Code</h5>
                        <button type="button"
                        class="btn-close box-content w-4 h-4 p-1 text-black border-none rounded-none opacity-50 focus:shadow-none focus:outline-none focus:opacity-100 hover:text-black hover:opacity-75 hover:no-underline"
                        aria-label="Close" @click="displayModalQrCode = false">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        </button>
                    </div>

                    <form @submit.prevent="uploadFile">
                        <BreezeValidationErrors class="px-4 my-2" />
                        <div class="modal-body relative p-4">
                            <div class="flex justify-center">
                                <div class="mb-4 xl:w-96">
                                    <label for="formFile" class="form-label inline-block mb-2 text-gray-700">Select Qr Code Status:</label>
                                    <select class="form-select appearance-none
                                    block
                                    w-full
                                    px-3
                                    py-1.5
                                    text-base
                                    font-normal
                                    text-gray-700
                                    bg-white bg-clip-padding bg-no-repeat
                                    border border-solid border-gray-300
                                    rounded
                                    transition
                                    ease-in-out
                                    m-0
                                    focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" aria-label="Default select example" v-model="uploadForm.status">
                                        <option value="Submitted">Submitted</option>
                                        <option value="onProcessing">On Processing</option>
                                        <option value="Processed">Processed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="flex justify-center">
                                <div class="mb-3 w-96">
                                    <label for="formFile" class="form-label inline-block mb-2 text-gray-700">Add Qr Code PDF File</label>
                                    <input class="form-control
                                    block
                                    w-full
                                    px-3
                                    py-1.5
                                    text-base
                                    font-normal
                                    text-gray-700
                                    bg-white bg-clip-padding
                                    border border-solid border-gray-300
                                    rounded
                                    transition
                                    ease-in-out
                                    m-0
                                    focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
                                    @input="uploadForm.file_pdf = $event.target.files[0]" type="file" id="qrCodeFile" accept = "application/pdf">
                                </div>
                            </div>
                        </div>
                        <div
                            class="modal-footer flex flex-shrink-0 flex-wrap items-center justify-end p-4 border-t border-gray-200 rounded-b-md">
                            <button type="button" class="px-6
                                py-2.5
                                bg-purple-600
                                text-white
                                font-medium
                                text-xs
                                leading-tight
                                uppercase
                                rounded
                                shadow-md
                                hover:bg-purple-700 hover:shadow-lg
                                focus:bg-purple-700 focus:shadow-lg focus:outline-none focus:ring-0
                                active:bg-purple-800 active:shadow-lg
                                transition
                                duration-150
                                ease-in-out" data-bs-dismiss="modal" @click="displayModalQrCode = false">Close
                            </button>
                            <button type="submit" class="px-6
                                py-2.5
                                bg-blue-600
                                text-white
                                font-medium
                                text-xs
                                leading-tight
                                uppercase
                                rounded
                                shadow-md
                                hover:bg-blue-700 hover:shadow-lg
                                focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0
                                active:bg-blue-800 active:shadow-lg
                                transition
                                duration-150
                                ease-in-out
                                ml-1">Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>

</template>


