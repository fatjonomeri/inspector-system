import { useState } from 'react'
import { useNavigate } from 'react-router-dom'

import {
  useAvailableJobs,
  useMyJobs,
  useAssignJob,
  useCompleteJob,
} from '@/hooks/useJobs'
import { Button } from '@/components/ui/button'
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { format } from 'date-fns'
import {
  LogOut,
  Calendar,
  MapPin,
  ClipboardList,
  CheckCircle2,
  Clock,
} from 'lucide-react'
import type { CustomJob } from '@/services/customJobService'
import { useAuth } from '@/contexts/AuthContext'

export default function DashboardPage() {
  const navigate = useNavigate()
  const { user, logout } = useAuth()
  const [selectedTab, setSelectedTab] = useState<'available' | 'my-jobs'>(
    'available',
  )
  const [selectedJob, setSelectedJob] = useState<CustomJob | null>(null)
  const [assignDate, setAssignDate] = useState('')
  const [completionData, setCompletionData] = useState({
    assessment: '',
    completedAt: '',
  })

  const { data: availableJobs, isLoading: loadingAvailable } =
    useAvailableJobs()

  const { data: myJobs, isLoading: loadingMyJobs } = useMyJobs()

  const assignMutation = useAssignJob()

  const completeMutation = useCompleteJob()

  const handleLogout = async () => {
    await logout()
    navigate('/login')
  }

  const handleAssign = async (job: CustomJob) => {
    if (!assignDate) {
      alert('Please select a scheduled date')
      return
    }
    try {
      await assignMutation.mutateAsync({
        id: job.id,
        payload: { scheduledDate: assignDate },
      })
      setSelectedJob(null)
      setAssignDate('')
    } catch (error: any) {
      alert(error.response?.data?.message || 'Failed to assign job')
    }
  }

  const handleComplete = async (job: CustomJob) => {
    if (!completionData.assessment) {
      alert('Please provide an assessment')
      return
    }
    try {
      await completeMutation.mutateAsync({
        id: job.id,
        payload: {
          assessment: completionData.assessment,
          completedAt: completionData.completedAt || undefined,
        },
      })
      setSelectedJob(null)
      setCompletionData({ assessment: '', completedAt: '' })
    } catch (error: any) {
      alert(error.response?.data?.message || 'Failed to complete job')
    }
  }

  const formatDate = (dateString: string) => {
    try {
      return format(new Date(dateString), 'PPp')
    } catch {
      return dateString
    }
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
      {/* Header */}
      <header className="bg-white shadow-sm">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
          <div>
            <h1 className="text-2xl font-bold text-gray-900">
              Inspector Dashboard
            </h1>
            <p className="text-sm text-gray-600">
              {user?.firstName} {user?.lastName}
            </p>
          </div>
          <Button variant="outline" onClick={handleLogout}>
            <LogOut className="h-4 w-4 mr-2" />
            Logout
          </Button>
        </div>
      </header>

      <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Tabs */}
        <div className="flex space-x-4 mb-6">
          <Button
            variant={selectedTab === 'available' ? 'default' : 'outline'}
            onClick={() => setSelectedTab('available')}
          >
            <ClipboardList className="h-4 w-4 mr-2" />
            Available Jobs
          </Button>
          <Button
            variant={selectedTab === 'my-jobs' ? 'default' : 'outline'}
            onClick={() => setSelectedTab('my-jobs')}
          >
            <CheckCircle2 className="h-4 w-4 mr-2" />
            My Jobs
          </Button>
        </div>

        {/* Available Jobs */}
        {selectedTab === 'available' && (
          <div className="space-y-4">
            {loadingAvailable && <p>Loading available jobs...</p>}
            {availableJobs?.length === 0 && (
              <Card>
                <CardContent className="py-8 text-center text-muted-foreground">
                  No available jobs at the moment
                </CardContent>
              </Card>
            )}
            {availableJobs?.map((job) => (
              <Card key={job.id}>
                <CardHeader>
                  <CardTitle className="flex items-start justify-between">
                    <span>{job.title}</span>
                    <span className="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">
                      Available
                    </span>
                  </CardTitle>
                  <CardDescription className="flex items-center gap-4 mt-2">
                    <span className="flex items-center">
                      <MapPin className="h-4 w-4 mr-1" />
                      {job.location}
                    </span>
                    <span className="flex items-center">
                      <Clock className="h-4 w-4 mr-1" />
                      {formatDate(job.createdAt)}
                    </span>
                  </CardDescription>
                </CardHeader>
                {selectedJob?.id === job.id ? (
                  <CardContent className="space-y-4">
                    <p className="text-sm text-gray-700">{job.description}</p>
                    <div className="space-y-2">
                      <Label htmlFor={`date-${job.id}`}>
                        Scheduled Date (in your timezone)
                      </Label>
                      <Input
                        id={`date-${job.id}`}
                        type="datetime-local"
                        value={assignDate}
                        onChange={(e) => setAssignDate(e.target.value)}
                      />
                    </div>
                    <div className="flex gap-2">
                      <Button
                        onClick={() => handleAssign(job)}
                        disabled={assignMutation.isPending}
                      >
                        {assignMutation.isPending
                          ? 'Assigning...'
                          : 'Confirm Assignment'}
                      </Button>
                      <Button
                        variant="outline"
                        onClick={() => setSelectedJob(null)}
                      >
                        Cancel
                      </Button>
                    </div>
                  </CardContent>
                ) : (
                  <CardContent>
                    <p className="text-sm text-gray-700 mb-4">
                      {job.description}
                    </p>
                    <Button onClick={() => setSelectedJob(job)}>
                      <Calendar className="h-4 w-4 mr-2" />
                      Assign to Me
                    </Button>
                  </CardContent>
                )}
              </Card>
            ))}
          </div>
        )}

        {/* My Jobs */}
        {selectedTab === 'my-jobs' && (
          <div className="space-y-4">
            {loadingMyJobs && <p>Loading your jobs...</p>}
            {myJobs?.length === 0 && (
              <Card>
                <CardContent className="py-8 text-center text-muted-foreground">
                  You don't have any assigned jobs yet
                </CardContent>
              </Card>
            )}
            {myJobs?.map((job) => (
              <Card key={job.id}>
                <CardHeader>
                  <CardTitle className="flex items-start justify-between">
                    <span>{job.title}</span>
                    <span
                      className={`text-xs px-2 py-1 rounded-full ${
                        job.status === 'completed'
                          ? 'bg-blue-100 text-blue-800'
                          : 'bg-yellow-100 text-yellow-800'
                      }`}
                    >
                      {job.status === 'completed' ? 'Completed' : 'Assigned'}
                    </span>
                  </CardTitle>
                  <CardDescription className="flex items-center gap-4 mt-2">
                    <span className="flex items-center">
                      <MapPin className="h-4 w-4 mr-1" />
                      {job.location}
                    </span>
                    {job.scheduledDate && (
                      <span className="flex items-center">
                        <Calendar className="h-4 w-4 mr-1" />
                        Scheduled: {formatDate(job.scheduledDate)}
                      </span>
                    )}
                  </CardDescription>
                </CardHeader>
                <CardContent className="space-y-4">
                  <p className="text-sm text-gray-700">{job.description}</p>
                  {job.status === 'completed' ? (
                    <div className="bg-blue-50 p-4 rounded-md">
                      <p className="text-sm font-medium text-blue-900 mb-2">
                        Assessment:
                      </p>
                      <p className="text-sm text-blue-800">{job.assessment}</p>
                      {job.completedAt && (
                        <p className="text-xs text-blue-600 mt-2">
                          Completed: {formatDate(job.completedAt)}
                        </p>
                      )}
                    </div>
                  ) : selectedJob?.id === job.id ? (
                    <div className="space-y-4">
                      <div className="space-y-2">
                        <Label htmlFor={`assessment-${job.id}`}>
                          Assessment *
                        </Label>
                        <Textarea
                          id={`assessment-${job.id}`}
                          value={completionData.assessment}
                          onChange={(e) =>
                            setCompletionData({
                              ...completionData,
                              assessment: e.target.value,
                            })
                          }
                          placeholder="Enter your inspection assessment..."
                          rows={4}
                        />
                      </div>
                      <div className="space-y-2">
                        <Label htmlFor={`completion-${job.id}`}>
                          Completion Date (optional, defaults to now)
                        </Label>
                        <Input
                          id={`completion-${job.id}`}
                          type="datetime-local"
                          value={completionData.completedAt}
                          onChange={(e) =>
                            setCompletionData({
                              ...completionData,
                              completedAt: e.target.value,
                            })
                          }
                        />
                      </div>
                      <div className="flex gap-2">
                        <Button
                          onClick={() => handleComplete(job)}
                          disabled={completeMutation.isPending}
                        >
                          {completeMutation.isPending
                            ? 'Submitting...'
                            : 'Submit Completion'}
                        </Button>
                        <Button
                          variant="outline"
                          onClick={() => {
                            setSelectedJob(null)
                            setCompletionData({
                              assessment: '',
                              completedAt: '',
                            })
                          }}
                        >
                          Cancel
                        </Button>
                      </div>
                    </div>
                  ) : (
                    <Button onClick={() => setSelectedJob(job)}>
                      <CheckCircle2 className="h-4 w-4 mr-2" />
                      Mark as Complete
                    </Button>
                  )}
                </CardContent>
              </Card>
            ))}
          </div>
        )}
      </main>
    </div>
  )
}
