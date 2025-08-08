<template>
	<div class="mt-2">
		<!-- Search -->
		<div v-if="showSearch">
			<b-form-input
				v-model="localFilter"
				placeholder="Search..."
				class="mb-3 custom-input"
			/>
		</div>

		<!-- Table -->
		<b-table
			class="custom-table"
			:items="paginatedData"
			:fields="fields"
			borderless
			hover
			small
			responsive
			v-bind="$attrs"
			@head-clicked="onSort"
		>
			<!-- Custom Headers with Sort -->
			<template v-for="col in computedFields" v-slot:[`head(${col.key})`]="data">
				<span @click="() => onSort(col.key)" style="cursor: pointer;">
					{{ col.label }}
					<i class="ms-1" v-if="sortKey === col.key"
						:class="sortOrder === 'asc' ? 'bi bi-sort-up' : 'bi bi-sort-down'" />
				</span>
			</template>

			<!-- Editable Cells -->
			<template v-for="col in editableFields" #[`cell(${col.key})`]="{ item }">
				<template v-if="isEditing(item)  && col.editable !== false">
					<input class="m-0 p-1 custom-input" v-if="col.type === 'text' || col.type === 'number'" :type="col.type" v-model="draft[col.key]" />
					<input class="m-0 p-1 custom-input" v-else-if="col.type === 'date'" type="date" v-model="draft[col.key]" />

					<input class="custom-input" v-else-if="col.type === 'boolean'" type="checkbox"  v-model="draft[col.key]"
						:true-value="1"
						:false-value="0" />
        		</template>

				<template v-else>
					<span v-if="col.type === 'boolean'">
						{{ item[col.key] == 1 ? 'True' : 'False' }}
					</span>
					<span v-else>
						{{ formatDisplayValue(item[col.key], col) }}
					</span>
				</template>
			</template>

			<!-- Actions -->
			<template #cell(actions)="data">
				<div class="d-flex justify-content-center align-items-center gap-2">
					<!-- Edit -->
					<button class="btn btn-link p-0 text-secondary" v-if="!isEditing(data.item)"
						@click="$emit('edit', data.item)">
						<i class="bi bi-pencil"></i>
					</button>

					<!-- Save -->
					<button class="btn btn-link p-0 text-success" v-else
						@click="$emit('save', data.item)">
						<i class="bi bi-check"></i>
					</button>

					<!-- Cancel -->
					<button class="btn btn-link p-0 text-muted" v-if="isEditing(data.item)"
						@click="$emit('cancel')">
						<i class="bi bi-x"></i>
					</button>
					
					<!-- Delete -->
					<!-- <button class="btn btn-link p-0 text-muted" v-if="isEditing(data.item)"
						@click="$emit('delete')">
						<i class="bi bi-x"></i>
					</button> -->
				</div>
			</template>
		</b-table>

		<!-- Pagination -->
		<b-pagination
			class="mt-3"
			align="center"
			v-model="currentPage"
			:per-page="perPage"
			:total-rows="filteredSortedData.length"
		/>
	</div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
	items: Array,
	fields: Array,
	perPage: {
		type: Number,
		default: 5,
	},
	showSearch: {
		type: Boolean,
		default: false,
	},
	currKey: [String, null],
	draft: Object,
	rowKey: {
		type: String,
		default: 'id',
	}
})

const emit = defineEmits(['edit', 'save', 'cancel'])

const localFilter = ref('')
const currentPage = ref(1)
const sortKey = ref('')
const sortOrder = ref('asc')

const isEditing = (row) => {
	return props.currKey === row[props.rowKey]
}

const editableFields = computed(() =>
	props.fields.filter(col => col.key !== 'actions')
)

const computedFields = computed(() =>
	props.fields.map(col => ({
		...col,
		key: col.key,
		label: col.label,
		type: col.type || 'text',
		thStyle: (col.thStyle || '') + ' cursor:pointer;',
		tdClass: col.tdClass || '',
		thClass: col.thClass || '',
		sortable: col.sortable !== false,
		formatter: col.formatter
	}))
)

const filteredSortedData = computed(() => {
	let data = [...props.items]

	if (localFilter.value) {
		const term = localFilter.value.toLowerCase()
		data = data.filter(row =>
			Object.values(row).some(val =>
				String(val).toLowerCase().includes(term)
			)
		)
	}

	if (sortKey.value) {
		data.sort((a, b) => {
			const aVal = a[sortKey.value] ?? ''
			const bVal = b[sortKey.value] ?? ''
			return sortOrder.value === 'asc'
				? String(aVal).localeCompare(String(bVal))
				: String(bVal).localeCompare(String(aVal))
		})
	}

	return data
})

const paginatedData = computed(() => {
	const start = (currentPage.value - 1) * props.perPage
	return filteredSortedData.value.slice(start, start + props.perPage)
})

const onSort = (field) => {
	if (field === 'actions') return

	if (sortKey.value === field) {
		sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
	} else {
		sortKey.value = field
		sortOrder.value = 'asc'
	}
}

const formatDisplayValue = (value, field) => {
	if (value === null || value === undefined) return '—'

	try {
		if (typeof field?.formatter === 'function') {
			return field.formatter(value)
		}

		switch (field.type) {
			case 'date':
				return new Date(value).toLocaleDateString('en-CA')
			case 'boolean':
				return value ? '1' : '0'
			default:
				return value
		}
	} catch (e) {
		console.warn('Error in formatDisplayValue:', e)
		return value
	}
}
</script>

<style scoped>
.text-nowrap {
	white-space: nowrap !important;
}

.b-form-input {
	border: 1px solid #dee2e6;
	width: 100%;
}

.btn:disabled {
	opacity: 0.5;
	cursor: not-allowed;
}

.custom-table .custom-input {
	font-size: .8rem;
	color: var(--text-dark);
}
</style>
