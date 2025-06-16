import { useState, useEffect, useCallback, useRef } from 'react';
import axios from 'axios';

export type Offer = {
  id: number;
  title: string;
  description: string;
  offered_by_type: string;
  offer_user: string;
  offer_company: string;
  logo_path: string;
  reward_total_cents: number;
  reward_offerer_percent: number;
  created_at: string;
  average_rating: number;
  status: "active" | "inactive" | "closed" | 'matched';
  industry: string;
};

export interface OfferFilters {
  title?: string;
  offer_company?: string;
  offered_by_type?: string;
  status?: string;
  average_rating_min?: number;
  created_at_from?: string;
}

export interface OfferSort {
  field: string;
  direction: 'asc' | 'desc';
}

export const useOffers = () => {
  const [offers, setOffers] = useState<Offer[]>([]);
  const [loading, setLoading] = useState(false);
  const [hasMore, setHasMore] = useState(true);
  const [currentPage, setCurrentPage] = useState(1);
  const [filters, setFilters] = useState<OfferFilters>({});
  const [sort, setSort] = useState<OfferSort>({ field: 'created_at', direction: 'desc' });
  
  // Use ref to track if we're currently fetching to prevent duplicate requests
  const isFetching = useRef(false);
  
  // Use refs to store current values for stable references
  const filtersRef = useRef(filters);
  const sortRef = useRef(sort);
  const currentPageRef = useRef(currentPage);
  
  // Update refs when state changes
  useEffect(() => { filtersRef.current = filters; }, [filters]);
  useEffect(() => { sortRef.current = sort; }, [sort]);
  useEffect(() => { currentPageRef.current = currentPage; }, [currentPage]);

  const buildQueryParams = useCallback((page: number, currentFilters: OfferFilters, currentSort: OfferSort) => {
    const params = new URLSearchParams();
    params.append('page', page.toString());
    params.append('per_page', '20');

    // Add sorting
    const sortPrefix = currentSort.direction === 'desc' ? '-' : '';
    params.append('sort', `${sortPrefix}${currentSort.field}`);

    // Add filters
    if (currentFilters.title) {
      params.append('filter[offer_title]', currentFilters.title);
    }
    if (currentFilters.offer_company) {
      params.append('filter[company.name]', currentFilters.offer_company);
    }
    if (currentFilters.offered_by_type) {
      params.append('filter[offered_by_type]', currentFilters.offered_by_type);
    }
    if (currentFilters.status) {
      params.append('filter[status]', currentFilters.status);
    }
    if (currentFilters.average_rating_min && currentFilters.average_rating_min > 0) {
      params.append('filter[average_rating_min]', currentFilters.average_rating_min.toString());
    }
    if (currentFilters.created_at_from) {
      params.append('filter[created_at][gte]', currentFilters.created_at_from);
    }

    return params.toString();
  }, []);

  const fetchOffers = useCallback(async (page: number, currentFilters: OfferFilters, currentSort: OfferSort, append: boolean = false) => {
    if (isFetching.current) return;
    
    isFetching.current = true;
    setLoading(true);

    try {
      const queryParams = buildQueryParams(page, currentFilters, currentSort);
      const { data } = await axios.get(`/api/v1/offers?${queryParams}`);
      
      if (append) {
        setOffers(prev => [...prev, ...data.data]);
      } else {
        setOffers(data.data);
      }
      
      setHasMore(data.current_page < data.last_page);
      setCurrentPage(data.current_page);
    } catch (error) {
      console.error('Error fetching offers:', error);
    } finally {
      setLoading(false);
      isFetching.current = false;
    }
  }, [buildQueryParams]);

  // Load initial data
  useEffect(() => {
    fetchOffers(1, {}, { field: 'created_at', direction: 'desc' }, false);
  }, []);

  // Load more data (infinite scroll)
  const loadMore = useCallback(() => {
    if (!loading && hasMore && !isFetching.current) {
      const nextPage = currentPageRef.current + 1;
      fetchOffers(nextPage, filtersRef.current, sortRef.current, true);
    }
  }, [loading, hasMore, fetchOffers]);

  // Apply new filters/sort (reset pagination)
  const applyFilters = useCallback((newFilters: OfferFilters, newSort?: OfferSort) => {
    const updatedSort = newSort || sort;
    setFilters(newFilters);
    if (newSort) {
      setSort(newSort);
    }
    setCurrentPage(1);
    setHasMore(true);
    fetchOffers(1, newFilters, updatedSort, false);
  }, [sort, fetchOffers]);

  return { 
    offers, 
    loading, 
    hasMore, 
    loadMore, 
    applyFilters,
    filters,
    sort
  };
};
